<?php namespace Nimbeo;

use \Template;

/**
 * Clase para generar consultas a la base de datos de reports
 */
class QueryGenerator {
	private $tables = [];
	private $ctes = [];
	private $wheres = [];
	private $templates = [];
	private $dateMap = [];
	private $dateFieldCnt = 0;
	private $dateSeriesSql = [];
	private $groupFields = [];
	private $selectFields = [];
	private $fieldCnt = 0;
	private $orderFields = [];

	/**
	  * Devuelve el tipo de base de datos correspondiente a un tipo CR (para castings)
	  */
	private function typeToCast($type)
	{
		$cast = null;
		switch ($type)
		{
			case 'color':
			case 'email':
			case 'file':
			case 'text':
			case 'textarea':
				//Texto: no necesario casting
				break;
			
			case 'checkbox':
			case 'radio':
			case 'select':
				//Id de campo: no necesario casting
				break;
			
			case 'date':
			case 'time':
				$cast = 'timestamp';
				break;
				
			case 'decimal':
				$cast = 'double precision';
				break;
			
			case 'number':
			case 'range':
			case 'report': //Id de report: int
				$cast = 'integer';
				break;
		}
		return $cast;
	}
	
	/**
	  * Devuelve el SQL correspondiente a un campo fijo de la tabla reports
	  */
	private function getFixedField($fieldid, $template)
	{
		switch ($fieldid)
		{
			case '_id':
				$ret = $template.'id';
				break;
			case '_title':
				//FIXME getTitle temporal mediante report-id, cambiar
				$ret = $template.'id';
				break;
			case '_latitude':
				$ret = 'ST_Y(ST_Centroid('.$template.'main_geo::geometry))';
				break;
			case '_longitude':
                $ret = 'ST_X(ST_Centroid('.$template.'main_geo::geometry))';
				break;
			case '_address':
				$ret = $template.'geodata #>> \'{formatted_address}\'';
				break;
			case '_date':
				$ret = $template.'created_at';
				break;
			default: //_cr_gs_X: Serie temporal
				$ret = $fieldid;
				break;
		}
		return $ret;
	}
	
	/**
	  * Genera el SQL correspondiente a un campo de la base de datos
	  * @param int $templateid
	  * @param string $fieldid
	  * @param map<id->template> $templates
	  * @param string $datetype
	  * @return SQL
	  */
	public function getSqlField($templateid, $fieldid, $datetype = null)
	{
		$template = '_cr_t'.$templateid.'_.';
		
		$ret = '';
		if ($fieldid[0] == '_')
		{
			//Campos predefinidos
			$ret = $this->getFixedField($fieldid, $template);
		}
		else
		{			
			//Devolver el campo
			$ret = $template.'datum #>> \'{'.$fieldid.', value}\'';
			
			//Extraer el tipo del campo
			$templ = $this->templates[$templateid];
			$field = $templ->getFieldById($fieldid);
			$cast = $this->typeToCast($field->type);
			if ($cast)
			{
				//Castear si es necesario
				$ret = "CAST($ret AS $cast)";
			}
		}

		//Para los campos de fecha, truncar si se solicita
		if ($datetype != null && $datetype != 'complete')
		{
			return "date_trunc('$datetype', $ret)";
		}
		return $ret;
	}
	
	public function addAggregate($function, $templateid, $fieldid, $filter = null)
	{
		$expr = ($function == 'count') ? 1 : $this->getSqlField($templateid, $fieldid);
		
		if ($filter)
		{
			$expr = 'CASE WHEN '.$this->expressionToSql($filter).' THEN '.$expr.' ELSE NULL END';
		}
		
		$this->selectFields[] = $function.'('.$expr.') AS f'.$this->fieldCnt++;
	}
	
	public function addSelect($templateid, $fieldid, $dateType)
	{
		$this->selectFields[] = $this->getSqlField(
					$templateid,
					array_get($this->dateMap, $fieldid, $fieldid),
					$dateType
					) . ' AS f'.$this->fieldCnt++;
	}
	
	public function addSelectExpression($expr, $alias = null)
	{
		if (!$alias)
			$alias = 'f'.$this->fieldCnt++;
		$this->selectFields[] = $this->expressionToSql($expr) . ' AS '.$alias;
	}
	
	public function addGroupBy($templateid, $fieldid, $dateType)
	{
		if ($dateType != null && $dateType != 'complete')
		{
			$dateField = '_cr_gs_'. $this->dateFieldCnt++;
			$originalField = $this->getSqlField($templateid, $fieldid);
			$truncField = $this->getSqlField($templateid, $fieldid, $dateType);
			
			//HACK: Quitar tabla del $originalField
			$originalField = preg_replace('/_cr_t\d+_\./', '', $originalField);
			
			//TODO: Cambiar por CTE's
			$this->dateSeriesSql[] = 'RIGHT JOIN generate_series('.
				"(SELECT min(date_trunc('$dateType', $originalField)) FROM reports WHERE template_id = " . $templateid . " AND deleted_at IS NULL),". 
				"(SELECT max(date_trunc('$dateType', $originalField)) FROM reports WHERE template_id = " . $templateid . " AND deleted_at IS NULL),".
				"'1 $dateType') $dateField ".
				"ON $truncField = $dateField";
			//TODO: Mapa de fechas por id de field -> id template+field
			$this->dateMap[$fieldid] = $dateField;
		}
		
		$this->groupFields[] = $this->getSqlField(
				$templateid,
				array_get($this->dateMap, $fieldid, $fieldid),
				$dateType);
	}
	
	public function addOrderBy($direction, $templateid, $fieldid, $dateType)
	{
		$this->orderFields[] = $this->getSqlField(
				$templateid,
				array_get($this->dateMap, $fieldid, $fieldid),
				$dateType
				) . ' ' . $direction;
	}
	
	public function addTemplate($templateid, $condition = null)
	{
		//Genera una CTE para cada template
		//Carga las templates incluidas en la consulta, para tener acceso a los tipos de datos de los campos
		$table = '_cr_t'.$templateid.'_';
		$this->tables[] = $table;
		$this->ctes[] = $table.' AS (SELECT * FROM reports WHERE template_id = '.$templateid.' AND deleted_at IS NULL)';
		
		if ($condition)
			$this->wheres[] = $condition;
		
		$this->templates[$templateid] = Template::find($templateid);
	}
	
	public function addWhere($where)
	{
		$this->wheres[] = $where;
	}
	
	public function expressionToSql($expr)
	{
		preg_match_all('/\$(\d+)\.([0-9a-fA-F]+|_[a-zA-Z]+)/', $expr, $matches, PREG_SET_ORDER);
		//                $ tpl  . customField |_fixedField
		
		foreach($matches as $match)
		{
			$newExpr = '(' . $this->getSqlField($match[1], $match[2]) . ')';
			
			$expr = str_replace($match[0], $newExpr, $expr);
		}
		return $expr;
	}
	
	public function toSql()
	{
		$sql = 'WITH '.implode(',', $this->ctes);
		$sql .= ' SELECT ';
	
		$sql .= implode(',', $this->selectFields);
		$sql .= ' FROM '.implode(',', $this->tables);
		$sql .= ' '.implode(' ', $this->dateSeriesSql);
		
		//Wheres
		if (!empty($this->wheres))
		{
			$newWheres = [];
			foreach($this->wheres as $where) {
				$newWheres[] = $this->expressionToSql($where);
			}
			$sql .= ' WHERE ' . implode(' AND ', $newWheres);
		}
			
		//Group SQL
		if ($this->groupFields)
			$sql .= ' GROUP BY '.implode(',', $this->groupFields);
			
		
		//Order
		if ($this->orderFields)
			$sql .= ' ORDER BY '.implode(',', $this->orderFields);
		
		return $sql;
	}
}

?>