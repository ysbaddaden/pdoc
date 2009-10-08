
<section>
  <h4>Classes</h4>
  <ul>
    <? foreach($this->parser->classes as $aside_klass): ?>
      <li><a href="<?= $this->klass_url($aside_klass) ?>"><?= $aside_klass['name'] ?></a></li>
    <? endforeach; ?>
  </ul>
</section>

<section>
  <h4>Methods</h4>
  <ul>
    <? /*foreach($this->parser->functions as $aside_klass): ?>
      <li><a href="<?= $this->klass_url($aside_klass) ?>"><?= $aside_klass['name'] ?></a></li>
    <? endforeach;*/ ?>
  </ul>
</section>

