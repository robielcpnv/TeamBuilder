<?php

require_once 'model/DB.php';

class Team
{

    public $id;
    public $name;
    public $state_id;

    public function __construct($id = null, $name = null, $state_id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->state_id = $state_id;
    }

    static public function make($fields) :Team
    {
        if (is_array($fields)){
            $res = DB::selectMany("SELECT id FROM `teams` order by id desc limit 1", []);
            return new Team($res[0]->id,$fields['name'], $fields['state_id']);
        }
        return new Team($fields->id,$fields->name, $fields->state_id);
    }

    public function create() :bool
    {
        try {
            if (isset($this->name) && isset($this->state_id)) {
                DB::insert('INSERT INTO `teams` (name,state_id) VALUES (:name,:state_id)',
                    ["name" => $this->name, "state_id" => $this->state_id]);
                return true;
            }
        } catch (PDOException $e){
            //echo $e->getMessage();
            return false;
        }

    }

    static public function find($id): null|Team
    {
        $select = DB::selectOne("SELECT * FROM `teams` where id = :id", ["id" => $id]);
        if($select != null){
            return self::make($select);
        }
        return null;
    }

    static public function all():array
    {
        return $res = DB::selectMany("SELECT * FROM `teams`", []);
    }

    public function save():bool
    {
        try {
            if($this->id != null){
                $sql = "UPDATE `teams` SET ";
                if($this->name != null){
                    $sql .= " `name` = :name,";
                }
                if($this->state_id != null){
                    $sql .= " `state_id` = :state_id,";
                }
                $sql = substr($sql,0,-1);
                $sql .= " WHERE id = :id;";

                $res = DB::execute( $sql,
                    ["id" => $this->id, "name" => $this->name, "state_id" => $this->state_id]);
                return true;
            }
        } catch (\PDOException $e) {
            //echo $e->getMessage();
            return false;
        }
    }

    public function delete():bool
    {
        return self::destroy($this->id);
    }

    static public function destroy($id):bool
    {
        try {
            DB::execute(' DELETE FROM `teams` WHERE id = :id', ["id" => $id]);
            return true;
        } catch (\PDOException $e) {
            //echo $e->getMessage();
            return false;
        }
    }
}