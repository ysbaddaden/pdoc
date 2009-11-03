
<ul class="attributes">
  <? foreach($attributes as $attr_name => $attr): ?>
    <li id="attribute-<?= $attr_name ?>">
      <span class="visibility"><?= $attr['visibility'] ?></span>
      <span class="name"><?= $attr_name ?></span>
      <span class="brief"><?= $this->span_to_html($attr['comment']) ?></span>
    </li>
  <? endforeach; ?>
</ul>

