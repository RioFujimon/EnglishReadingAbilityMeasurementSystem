<?php
if ( ! empty($this->viewVars['EramsError']) ) {
   return;
}
if ( ! empty($this->viewVars['EramsTop']) || ! empty($this->viewVars['EramsBackTo']) ) {
    echo '<div class="spacer"></div>'."\n";
    echo '<div class="formset">'."\n";
    if ( ! empty($this->viewVars['EramsTop']) ) {
        echo $this->Form->create('null', [
            'type' => 'get',
            'url' => [ 'controller' => $this->viewVars['EramsTop'], 'action' => null ]]).
            $this->Form->button('教員トップページに戻る').
            $this->Form->end()."\n";
    }
    if ( ! empty($this->viewVars['EramsBackTo']) ) {
        echo $this->Form->create('null', [
            'type' => 'get',
            'url' => [ 'controller' => $this->viewVars['EramsBackTo'], 'action' => null ]]);
        if ( ! empty($this->viewVars['EramsBackToOpt']) ) {
            foreach ( $this->viewVars['EramsBackToOpt'] as $key => $val ) {
                echo $this->Form->hidden( $key, [ 'value' => $val ] );
            }
        }
        echo $this->Form->button('上の階層に戻る').
            $this->Form->end()."\n";
    }
    echo '</div>'."\n";
}
?>
