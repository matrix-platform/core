<?php //>

namespace matrix\web\backend;

use matrix\db\Criteria;

class SearchController extends Controller {

    private $columns;

    public function __construct($name) {
        $this->values = [
            'defaultRanking' => true,
            'table' => table($name),
            'view' => 'backend/search.php',
        ];
    }

    public function getColumns() {
        if ($this->columns === null) {
            $this->columns = $this->table()->getColumns($this->columns());
        }

        return $this->columns;
    }

    protected function process($form) {
        $keyword = @$form['keyword'];
        $conditions = $this->conditions() ?: [];

        if ($keyword !== null) {
            $criteria = Criteria::createOr();

            foreach ($this->getColumns() as $name => $column) {
                if ($column->searchStyle() === 'like') {
                    $criteria->add($column->like("%{$keyword}%", true));
                } else if ($column->validate($keyword) === true) {
                    $criteria->add($column->equal($keyword));
                }
            }

            $conditions[] = $criteria;
        }

        $model = $this->table()->model();
        $data = $model->query($conditions, $this->defaultRanking());

        foreach ($data as &$item) {
            $item['.title'] = $model->toString($item);
        }

        return $this->subprocess($form, [
            'success' => true,
            'data' => $data,
        ]);
    }

}
