<?php

class Navigation extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'navigation';

    public function parent() {

        return $this->hasOne('navigation', 'id', 'parent_id');

    }

    public function children() {

        return $this->hasMany('navigation', 'parent_id', 'id');

    }  

    public static function tree() {


        return static::with(implode('.', array_fill(0, 4, 'children')))->where('parent_id', '=', NULL)->get();

    }

    public static function getShowMenu()
    {
        
        // Enfermedades asociadas
        $q =  " SELECT e.*, e.datum->'5889e63a4646c0'->>'value' as nombre  "
                ."  FROM reports p, reports e , reports persona "
                ."  WHERE persona.template_id= 10 "
                ."          AND p.template_id = 11   "
                ."          AND e.template_id = 2  "
                ."          AND (persona.datum->'5889efb0944a90'->>'value')::int = ". Auth::user()->id
                ."          AND (p.datum->'5889f0a9092ea1'->>'value')::int = persona.id "
                ."          AND (p.datum->'5922aef15ccad0'->>'value')::int = e.id   "
                ."          AND persona.deleted_at IS NULL " 
                ."          AND e.deleted_at IS NULL " 
                ."          AND p.deleted_at IS NULL ";

        $enfermedadesAsociadas = DB::select(DB::raw($q));

        $list = [];
        foreach( $enfermedadesAsociadas as $enfermedad ){
            
            if ($enfermedad->id == 637) {   //Hiperpresión arterial
                $list[27] = Template::where("id", '=', 27)->get();
                $list[28] = Template::where("id", '=', 28)->get();
                $list[37] = Template::where("id", '=', 37)->get();
                $list[48] = Template::where("id", '=', 48)->get();   ///frecuencia cardiaca
            }
            if($enfermedad->id == 635 || $enfermedad->id == 636 ){ //Diabetes
                $list[25] = Template::where("id", '=', 25)->get();
                $list[27] = Template::where("id", '=', 27)->get();
                $list[28] = Template::where("id", '=', 28)->get();
                $list[37] = Template::where("id", '=', 37)->get();
            }
            if($enfermedad->id >= 630 && $enfermedad->id <= 634 ){ //Hepatitis 
                $list[27] = Template::where("id", '=', 27)->get();
                $list[28] = Template::where("id", '=', 28)->get();
                $list[37] = Template::where("id", '=', 37)->get();
                $list[48] = Template::where("id", '=', 48)->get();
                $list[49] = Template::where("id", '=', 49)->get();
            }
        }
        return $list;
        
    }

}