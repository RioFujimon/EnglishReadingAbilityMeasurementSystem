<?php

namespace App\Controller\Component;

use Cake\Controller\Component;

class EramsComponent extends Component {

    public function initialize(array $config) {
        // コントローラを取得
        $this->controller = $this->_registry->getController();
    }
    
    public function setTitle($title) {
        // タイトル変数をセットするだけ
        $this->controller->set('EramsTitle', $title);
    }

    public function setCss($css) {
        // Css 変数をセットするだけ
        $this->controller->set('EramsCss', $css);
    }

    public function setBackPage($url, $getopt = null) {
        $this->controller->set('EramsBackTo', $url);
        if ( $getopt != null && is_array($getopt) ) {
            $this->controller->set('EramsBackToOpt', $getopt);
        }
    }
    public function error($mess) {
        // セッションを取得して廃棄
        $session = $this->controller->request->getSession();
        $session->destroy();
        // デバッグ用のメッセージの要素を取得
        $dbg = debug_backtrace();
        $dbg_class = $dbg[1]['class'];
        $dbg_type = $dbg[1]['type'];
        $dbg_func = $dbg[1]['function'];
        $dbg_file = basename($dbg[1]['file']);
        $dbg_line = $dbg[1]['line'];
        // メッセージ中の改行コードの処理
        $mess = preg_replace('/\n+$/', '', $mess);
        $mess = preg_replace('/\n+/', '<br>', $mess);
        // コントローラの 'error' にデータを設定
        $this->controller->
            set('EramsError', $mess."<br>".
            '[ Call from '.$dbg_class.$dbg_type.$dbg_func.'() in "'.
            $dbg_file.'" on line '.$dbg_line.'. ]'
            );
        // 自動レンダリングをオフにする
        $this->controller->disableAutoRender();
        // テンプレートパスを変更する
        $this->controller->viewBuilder()->setTemplatePath('Error');
        // レンダリングを変更する
        $this->controller->render("index");
    }

    public function checkAdmin() {
        // セッションを取得
        $session = $this->request->getSession();
        // セッションに uid, gid, uname, gname が設定されているか確認
        $uid = $session->read('Erams.uid');
        $gid = $session->read('Erams.gid');
        $uname = $session->read('Erams.uname');
        $gname = $session->read('Erams.gname');
        // uid, gid, uname, gname の内容を確認してエラーがあれば終了
        if ( $uid === null || $gid === null ||
        empty($uname) || empty($gname) ||
        $uid != '1' || $gid != '1' ) {
            $this->controller->Erams->setTitle('管理者認証エラー');
            $this->controller->Erams->error(
                "このページにアクセスするには管理者権限が必要です。\n".
                "セッション情報のタイムアウトや".
                "不正なページ遷移なども原因かもしれません。");
        }
    }

    public function checkUser() {
        // セッションを取得
        $session = $this->request->getSession();
        // セッションに uid, gid, uname, gname が設定されているか確認
        $uid = $session->read('Erams.uid');
        $gid = $session->read('Erams.gid');
        $uname = $session->read('Erams.uname');
        $gname = $session->read('Erams.gname');
        // uid, gid, uname, gname の内容を確認してエラーがあれば終了
        if ( $uid === null || $gid === null ||
        empty($uname) || empty($gname) ) {
            $this->controller->Erams->setTitle('ユーザ認証エラー');
            $this->controller->Erams->error(
                "ユーザ情報の確認に失敗しました。\n".
                "セッション情報のタイムアウトや".
                "不正なページ遷移などの原因が考えられます。");
        }
    }       
}
?>
