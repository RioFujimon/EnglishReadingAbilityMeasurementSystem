<?php
echo $this->Element('Info', [ 'info' =>
"・テストを受ける際には、ブラウザの「戻る」や「再読み込み」ボタンを利用してはいけません。\n\n".
"これから実施するテストでは、次のような英文（例）が表示されます。\n".
"(1) まず、英文が読みやすいようにブラウザの横幅や高さを調整して下さい。\n".
"(2) 調整が終わったら画面下部の「次のページに移動」ボタンを押して下さい。\n"
]);
?>
<div class="reader">
THERE WAS ONCE a town in the heart of America where all life seemed to live in harmony with its surroundings. The town lay in the midst of a checkerboard of prosperous farms, with fields of grain and hillsides of orchards where, in spring, white clouds of bloom drifted above the green fields. In autumn, oak and maple and birch set up a blaze of color that flamed and flickered across a backdrop of pines. Then foxes barked in the hills and deer silently crossed the fields, half hidden in the mists of the fall mornings.<br>
&nbsp; Along the roads, laurel, viburnum and alder, great ferns and wildflowers delighted the traveler's eye through much of the year. Even in winter the roadsides were places of beauty, where countless birds came to feed on the berries and on the seed heads of the dried weeds rising above the snow. The countryside was, in fact, famous for the abundance and variety of its bird life, and when the flood of migrants was pouring through in spring and fall people traveled from great distances to observe them. Others came to fish the streams, which flowed clear and cold out of the hills and contained shady pools where trout lay. So it had been from the days many years ago when the first settlers raised their houses, sank their wells, and built their barns.<br>
&nbsp; Then a strange blight crept over the area and everything began to change. Some evil spell had settled on the community: mysterious maladies swept the flocks of chickens; the cattle and sheep sickened and died. Everywhere was a shadow of death. The farmers spoke of much illness among their families. In the town the doctors had become more and more puzzled by new kinds of sickness appearing among their patients. There had been several sudden and unexplained deaths, not only among adults but even among children, who would be stricken suddenly while at play and die within a few hours.<br>
&nbsp; There was a strange stillness. The birds, for example— where had they gone? Many peoplespoke of them, puzzled and disturbed. The feeding stations in the backyards were deserted. The few birds seen anywhere were moribund; they trembled violently and could not fly. It was a spring without voices. On the mornings that had once throbbed with the dawn chorus of robins, catbirds, doves, jays, wrens, and scores of other bird voices there was now no sound; only silence lay over the fields and woods and marsh.</br>
&nbsp; On the farms the hens brooded, but no chicks hatched. The farmers complained that they were unable to raise any pigs— the litters were small and the young survived only a few days. Theapple trees were coming into bloom but no bees droned among the blossoms, so there was no pollination and there would be no fruit.<br>
&nbsp; The roadsides, once so attractive, were now lined with browned and withered vegetation as though swept by fire. These, too, were silent, deserted by all living things. Even the streams were now lifeless. Anglers no longer visited them, for all the fish had died.<br>
&nbsp; In the gutters under the eaves and between the shingles of the roofs, a white granular powder still showed a few patches; some weeks before it had fallen like snow upon the roofs and the lawns, the fields and streams.<br>
No witchcraft, no enemy action had silenced the rebirth of new life in this stricken world. The people had done it themselves.<br>
</div>
<?php
// フォームの開始
echo $this->Form->create("null", [
    "type" => "get",
    "url" => [
        "controller" => "PreTest",
        "action" => "phase1" ]
])."\n";

// フォームを配置
echo '<div class="center">'."\n";
echo $this->Form->button('次のページに移動')."\n";
echo $this->Form->hidden('eid', [ 'value' => $this->viewVars['Erams.Test.Eid'] ]);
echo '</div>'."\n";
// フォームの終了
echo $this->Form->end();

?>
