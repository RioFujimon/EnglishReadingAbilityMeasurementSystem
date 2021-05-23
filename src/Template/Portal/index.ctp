<div class="label">ポータルページメニュー</div>
<ul> 
<li><?= $this->Html->link('管理者（教員）ログインページ',
    ['controller' => 'AdminLogin' ]); ?></li>
<li><?= $this->Html->link('学生ログインページ',
    ['controller' => 'UserLogin' ]); ?></li>
</ul>
