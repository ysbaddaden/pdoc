
<section>
  <h4>Classes</h4>
  <ul>
    <? foreach($this->parser->list_classes() as $aside_name => $aside_klass): ?>
      <? if (!isset($aside_klass['visibility']) or $aside_klass['visibility'] != 'private'): ?>
        <li><a href="<?= $this->klass_url($aside_name) ?>"><?= $aside_name ?></a></li>
      <? endif; ?>
    <? endforeach; ?>
  </ul>
</section>

<section>
  <h4>Methods</h4>
  <ul>
    <? foreach($this->parser->list_methods() as $aside_name => $aside_func): ?>
      <? if (!isset($aside_func['visibility']) or $aside_func['visibility'] != 'private'): ?>
        <li><a href="<?= $this->relative_url().$aside_func['link'] ?>"><?= $aside_name ?></a></li>
      <? endif; ?>
    <? endforeach; ?>
  </ul>
</section>

