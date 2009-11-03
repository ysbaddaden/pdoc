<? foreach($methods as $method_name => $method): ?>
  <article class="method <?= $method['visibility'] ?>" id="method-<?= $method_name ?>">
    <h3>
      <span class="name"><?= $method_name ?></span><span class="arguments">(<?= $method['arguments'] ?>)</span>
    </h3>
    
    <? if (!empty($method['comment'])): ?>
      <div class="description">
        <?= $this->text_to_html($method['comment'], array('headings_start' => 4)) ?>
      </div>
    <? endif; ?>
  </article>
<? endforeach; ?>
