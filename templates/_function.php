
<article class="function" id="function-<?= $function['name'] ?>">
  <h3>
    <span class="name"><?= $function['name'] ?></span><span class="arguments">(<?= $function['arguments'] ?>)</span>
  </h3>
  
  <div class="description">
    <?= $this->fix_internal_links(text_to_html($function['description'], array('headings_start' => 4))) ?>
  </div>
</article>

