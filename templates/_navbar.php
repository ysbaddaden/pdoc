
<dl class="classes">
  <? if (!empty($this->parser->classes)): ?>
    <dt>Classes:</dt>
    
    <? foreach($this->parser->classes as $_klass): ?>
      <? $resource = "class-{$_klass['name']}.html" ?>
      <dd>
        <? if (isset($klass) and $_klass['name'] == $klass['name']): ?>
          <?= $_klass['name'] ?>
        <? else: ?>
          <a href="<?= $resource ?>" title="Class: <?= $_klass['name'] ?>"><?= $_klass['name'] ?></a>
        <? endif; ?>
      </dd>
    <? endforeach; ?>
  <? endif; ?>
  
  <? if (!empty($this->parser->functions)): ?>
    <dt>Functions:</dt>
    
    <? foreach($this->parser->functions as $_func): ?>
      <? $resource = "function-{$_func['name']}.html" ?>
      <dd>
        <? if (isset($function) and $_func['name'] == $function['name']): ?>
          <?= $_func['name'] ?>
        <? else: ?>
          <a href="<?= $resource ?>" title="Function: <?= $_func['name'] ?>"><?= $_func['name'] ?></a>
        <? endif; ?>
      </dd>
    <? endforeach; ?>
  <? endif; ?>
</dl>

