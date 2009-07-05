<? foreach($methods as $method): ?>
  <? $resource = "#{$method['name']}" ?>
  
  <article class="method <?= $method['visibility'] ?>" id="method-<?= $method['name'] ?>">
    <h3>
      <span class="name"><?= $method['name'] ?></span><span class="arguments">(<?= $method['arguments'] ?>)</span>
    </h3>
    
    <p class="brief"><?= $method['brief'] ?></p>
    
    <div class="description">
      <?= $method['description'] ?>
    </div>
  </article>
<? endforeach; ?>
