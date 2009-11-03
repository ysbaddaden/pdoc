
<ul class="constants">
  <? foreach($constants as $const_name => $const): ?>
    <li id="constant-<?= $const_name ?>">
      <span class="name">
        <?= $const_name ?> = <?= $const['value'] ?>
      </span>
      <span class="brief"><?= $this->span_to_html($const['comment']) ?></span>
    </li>
  <? endforeach; ?>
</ul>

