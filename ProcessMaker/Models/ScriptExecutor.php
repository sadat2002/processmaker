<?php

namespace ProcessMaker\Models;

use Illuminate\Database\Eloquent\Model;
use ProcessMaker\Traits\HasVersioning;

class ScriptExecutor extends Model
{
    use HasVersioning;

    protected $fillable = [
        'title', 'description', 'language', 'config'
    ];

    public static function install($params)
    {
        $language = $params['language'];
        $initialExecutor = self::initialExecutor($language);

        if ($initialExecutor) {
            $initialExecutor->update($params);
        } else {
            $initialExecutor = self::create($params);
            Script::where('language', $language)->update(['script_executor_id' => $initialExecutor->id]);
            ScriptVersion::where('language', $language)->update(['script_executor_id' => $initialExecutor->id]);
        }
        
        return $initialExecutor;
    }

    public static function initialExecutor($language)
    {
        return self::where('language', $language)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function versions()
    {
        return $this->hasMany(ScriptExecutorVersion::class);
    }

    public static function initDockerfile($language)
    {
        // remove try/catch block after lang packages updated
        try {
            $dockerfile = file_get_contents(self::packagePath($language) . '/Dockerfile');
        } catch (\ErrorException $e) {
            $dockerfile = '';
        }
        $initDockerfile = config('script-runners.' . $language . '.init_dockerfile');
        
        // remove check after lang packages updated
        if (!is_array($initDockerfile)) {
            $initDockerfile = explode("\n", $initDockerfile);
        }
        $dockerfile .= "\n" . implode("\n", $initDockerfile);

        return $dockerfile;
    }

    public static function packagePath($language)
    {
        $config = config('script-runners');
        if (isset($config[$language])) {
            throw new \ErrorException("Language not in config: " . $language);
        }
        return config('script-runners.' . $language . '.package_path');
    }
}
