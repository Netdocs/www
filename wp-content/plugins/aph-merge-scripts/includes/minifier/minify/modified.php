<?php
// Change minify.php file

abstract class Minify
{
	private $toPath = '';
	
	public function setToPath($path)
	{
		$this->toPath = $path;
	}
	
	public function minify($path = null)
    {
		$toPath  = $this->toPath ?: $path;
        $content = $this->execute($toPath);

        // save to path
        if ($path !== null) {
            $this->save($content, $path);
        }

        return $content;
    }
}