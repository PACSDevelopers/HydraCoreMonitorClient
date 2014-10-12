<?hh // decl

namespace HC\DB2;

/**
 * Class DB2
 */
class Query
{
    protected Vector $tokens;


    public function __construct($settings = [])
    {
        $this->tokens = Vector {};
    }

    public function select(Vector<Map> $rows) :Query {
        $this->tokens[] = Map {'select' => $rows};
        return $this;
    }

    public function delete(string $table) :Query {
        $this->tokens[] = Map {'from' => $table};
        return $this;
    }

    public function insert(string $table, Map $map) :Query {
        $this->tokens[] = Map {'insert' => Vector {$table, $map}};
        return $this;
    }

    public function update(string $table, Map $map) :Query {
        $this->tokens[] = Map {'update' => Vector {$table, $map}};
        return $this;
    }

    public function from(string $table, string $uniqueID) :Query {
        $this->tokens[] = Map {'from' => Vector {$table, $uniqueID}};
        return $this;
    }

    public function leftJoin(string $table, string $uniqueID) :Query {
        $this->tokens[] = Map {'leftJoin' => Vector {$table, $uniqueID}};
        return $this;
    }

    public function rightJoin(string $table, string $uniqueID) :Query {
        $this->tokens[] = Map {'rightJoin' => Vector {$table, $uniqueID}};
        return $this;
    }

    public function join(string $table, string $uniqueID) :Query {
        $this->tokens[] = Map {'join' => Vector {$table, $uniqueID}};
        return $this;
    }

    public function where(Vector<Map> $rows) :Query {
        $this->tokens[] = Map {'where' => $rows};
        return $this;
    }

    public function orderBy(Map $map) :Query {
        $this->tokens[] = Map {'orderBy' => $map};
        return $this;
    }

    public function limit(Integer $start, Integer $end) :Query {
        $this->tokens[] = Map {'limit' => Vector{$start, $end}};
        return $this;
    }

    public function groupBy(Map $map) :Query {
        $this->tokens[] = Map {'groupBy' => $map};
        return $this;
    }
}
