<?php

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;

trait GeoJSONTrait {

    /**
     * GeoJSONTrait. See also Report model.
     *
     * @return void
     */
    public static function bootGeoJSONTrait()
    {
        static::addGlobalScope(new GeoJSONScope);
    }
}

class GeoJSONScope implements ScopeInterface {

	public function apply(Builder $builder)
	{
		//Añade el campo geo, como codificación GeoJson del main_geo
		$builder->selectRaw("ST_AsGeoJSON(main_geo, 5, CASE WHEN GeometryType(main_geo) = 'POINT' THEN 0 ELSE 1 END) as geo")->addSelect('*');
	}

	public function remove(Builder $builder)
	{
	}
}
?>