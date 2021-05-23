<?php
if ( ! empty($this->viewVars['EramsLoginInfo']) ) {
    echo '<div class="spacer"></div>'."\n";
    echo '<div class="user_info">'."\n";
    echo 'Group: '.$this->viewVars['EramsLoginInfo']['gname'].', '.
        'User: '.$this->viewVars['EramsLoginInfo']['uname'];
    echo '</div>'."\n";
}
?>
