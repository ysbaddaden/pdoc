
<ul class="attributes">
  <? foreach($attributes as $attribute): ?>
    <? $resource = "#{$attribute['name']}" ?>
    
    <li id="attribute-<?= $attribute['name'] ?>">
      <span class="visibility"><?= $attribute['visibility'] ?></span>
      <span class="name">$<?= $attribute['name'] ?></span>
      <span class="brief"><?= $this->fix_internal_links(span_to_html($attribute['brief'])) ?></span>
    </li>
  <? endforeach; ?>
</ul>

