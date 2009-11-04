
<article class="function" id="function-<?= $func_name ?>">
  <h3>
    <span class="name"><?= $func_name ?></span><span class="arguments">(<?= $func['arguments'] ?>)</span>
  </h3>
  
  <div class="description">
    <?= $this->text_to_html($func['comment'], array('headings_start' => 4)) ?>
  </div>
</article>

