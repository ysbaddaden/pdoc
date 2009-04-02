<?php

# Browses a directory, looking for some files.
class PDoc_Browser
{
  public $basedir;
  
  function __construct($basedir)
  {
    $this->basedir = $basedir;
    if (!is_dir($basedir)) {
      throw new Exception("No such directory: $basedir.");
    }
  }
  
  # Searches a directory for files matching the given file +extension+.
  function & search($extension)
  {
    return $this->recursive_search($this->basedir, $extension);
  }
  
  # Recursively parses a directory,
  # extracting files that match the +extension+.
  protected function & recursive_search($dir, $extension)
  {
#    echo "Searching in $dir/\n";
    
    $files = array();
    if ($dh = opendir($dir))
    {
      while (($file = readdir($dh)) !== false)
      {
        if ($file == '.' or $file == '..' or substr($file, 0, 1) == '.') {
          continue;
        }
        $path = $dir.'/'.$file;
        
        if (is_dir($path)) {
          $files = array_merge($files, $this->recursive_search($path, $extension));
        }
        elseif (preg_match('/\.'.$extension.'$/', $file))
        {
#          echo "Found: $path\n";
          $files[] = $path;
        }
      }
      closedir($dh);
    }
    return $files;
  }
}

?>
