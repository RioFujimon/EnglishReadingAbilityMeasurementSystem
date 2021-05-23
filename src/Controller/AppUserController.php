<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

class AppUserController extends AppController {

    public function initialize() {
        // スーパークラスを初期化
        parent::initialize();
        // ユーザ用の Css を読み込む
        $this->Erams->setCss('erams_user.css');
        // ユーザが教員の場合には教員トップページに戻るボタン表示用のデータを作成
        $session = $this->request->getSession();
        $uid = $session->read('Erams.uid');
        if ( $uid == '1' ) {
            $this->set("EramsTop", "/AdminTop");
        }
        // ユーザとしてログイン済みかチェックする
        $this->Erams->checkUser();
        // viewVar をセットする
        $session->read('Erams.uname');

    }
}
