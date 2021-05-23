<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <title><?= $this->viewVars['EramsTitle'] ?></title>
    <?= $this->Html->meta('icon')."\n" ?>
    <?= $this->Html->css( $this->viewVars['EramsCss'] )."\n" ?>
    <!--?= $this->Html->css('erams_user') ?-->
    <?= $this->Html->script('erams')."\n" ?>
</head>
<body>
<div class="spacer"></div>
<div class="header">英文読解能力測定システム：<?= $this->viewVars['EramsTitle'] ?></div>
<?= $this->Element("UserInfo") ?>
<?= $this->Element("BackTo") ?>
<div class="spacer"></div>
<?= $this->Flash->render() ?>
<div class="spacer"></div>
<?= $this->fetch('content') ?>
<div class="spacer"></div>
<?= $this->Element("BackTo") ?>
<div class="spacer"></div>
<div class="footer"></div>
</body>
</html>
