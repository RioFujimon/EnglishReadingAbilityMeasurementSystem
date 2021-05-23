<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

class EramsDBComponent extends Component {

    public function initialize(array $config) {
        // コントローラを取得
        $this->controller = $this->_registry->getController();
    }

    public function delInstitute($id) {
    }

    
    /**
     * finders : [ 'id' => [1,2,3...], 'gid' => [1,2,3] ] の形
     */
    public function get($table, $sort = null, array $finders = null) {
        // テーブルを確保
        $table = $this->controller->getTableLocator()->get($table);
        // ソートオプションの処理
        if ( $sort == null ) {
            $sort = array();
        } else if ( ! is_array($sort) ) {
            $sort = array($sort);
        }
        // 基本クエリーを生成
        if ( count($sort) == 0 ) {
            $q = $table->find('all');
        } else {
            $q = $table->find('all', ['order' => $sort]);
        }
        // ファインダーの構築
        $wherelist = array();
        if ( $finders != null ) {
            foreach ( $finders as $key => $val ) {
                if ( empty($wherelist[$key]) ) {
                    $wherelist[$key] = array();
                }
                if ( ! is_array($val) ) {
                    $wherelist[$key][] = [ $key => $val ];
                } else {
                    foreach ( $val as $v ) {
                        $wherelist[$key][] = [ $key => $v ];
                    }
                }
            }
            if ( count($wherelist) != 0 ) {
                foreach ( $wherelist as $key => $val ) {
                    $q = $q->where( ['OR' => $val] );
                }
            }
        }
        // 結果を返却
        return $q->all();
    }
}
?>
