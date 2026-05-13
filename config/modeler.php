<?php

$generated = config_path('_generated/modeler.php');
$generatedConfig = is_file($generated) ? require $generated : [];

$binExt = PHP_OS_FAMILY === 'Windows' ? '.exe' : '';

return [
    'setting' => [
        'TRANS_API_KEY' => env('MODELER_TRANS_API_KEY', ''),
        'TRANS_API_SECRET' => env('MODELER_TRANS_API_SECRET', ''),
        'TRANS_PROVIDER' => env('MODELER_TRANS_PROVIDER', ''), //BAIDU,ALIYUN,TENCENT
        'TRANS_PROXY' => env('MODELER_TRANS_PROXY', ''),
        'LLM_PROVIDER' => env('MODELER_LLM_PROVIDER', ''), //DEEPSEEK,DOUBAO,QWEN,GLM,OPENAI,CLAUDE
        'LLM_API_KEY' => env('MODELER_LLM_API_KEY', ''),
        'LLM_PROXY' => env('MODELER_LLM_PROXY', ''),
        'PRO_SN' => env('MODELER_PRO_SN', ''),
        'STUDIO_SERVER_PORT' => env('MODELER_STUDIO_SERVER_PORT', "auto"),
        'STUDIO_AUTO_OPEN' => true,
        'STUDIO_PATH' => env('MODELER_STUDIO_PATH', base_path('.modeler/bin/studio' . $binExt)),
        'GENERATOR_PATH' => env('MODELER_GENERATOR_PATH', base_path('.modeler/bin/generator' . $binExt)),
        'PROJECT_NAME' => env('MODELER_PROJECT_NAME', env('APP_NAME', 'Laravel')),
        'PROJECT_DIR' => env('MODELER_PROJECT_DIR', base_path()),
        'TEMPLATES_PATH' => env('MODELER_TEMPLATES_PATH', base_path('.modeler/templates')),
        'DATA_PATH' => env('MODELER_DATA_PATH', base_path('.modeler/data')),
        'LOG_PATH' => env('MODELER_LOG_PATH', base_path('.modeler/runtime/logs')),
        'RUNTIME_PATH' => env('MODELER_RUNTIME_PATH', base_path('.modeler/runtime')),
        'PROMPT_PATH' => env('MODELER_PROMPT_PATH', base_path('.modeler/prompt')),
        'TRANS_BAIDU_API_URL' => env('MODELER_TRANS_BAIDU_API_URL', 'https://fanyi-api.baidu.com/api/trans/vip/translate'),
        'TRANS_ALIYUN_API_URL' => env('MODELER_TRANS_ALIYUN_API_URL', 'https://mt.cn-hangzhou.aliyuncs.com/api/translate/web/ecommerce'),
        'TRANS_TENCENT_API_HOST' => env('MODELER_TRANS_TENCENT_API_HOST', 'tmt.tencentcloudapi.com'),
        'TRANS_TENCENT_API_VERSION' => env('MODELER_TRANS_TENCENT_API_VERSION', '2018-03-21'),
        'TRANS_TENCENT_API_ACTION' => env('MODELER_TRANS_TENCENT_API_ACTION', 'TextTranslate'),
        'TRANS_TENCENT_API_REGION' => env('MODELER_TRANS_TENCENT_API_REGION', 'ap-guangzhou'),
        'LLM_DOUBAO_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_DOUBAO_CHAT_COMPLETIONS_URL', 'https://ark.cn-beijing.volces.com/api/v3/chat/completions'),
        'LLM_DOUBAO_MODEL_ID' => env('MODELER_LLM_DOUBAO_MODEL_ID', ''),
        'LLM_DEEPSEEK_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_DEEPSEEK_CHAT_COMPLETIONS_URL', 'https://api.deepseek.com/chat/completions'),
        'LLM_DEEPSEEK_MODEL_ID' => env('MODELER_LLM_DEEPSEEK_MODEL_ID', 'deepseek-chat'),
        'LLM_QWEN_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_QWEN_CHAT_COMPLETIONS_URL', 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions'),
        'LLM_QWEN_MODEL_ID' => env('MODELER_LLM_QWEN_MODEL_ID', 'qwen-plus'),
        'LLM_GLM_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_GLM_CHAT_COMPLETIONS_URL', 'https://open.bigmodel.cn/api/paas/v4/chat/completions'),
        'LLM_GLM_MODEL_ID' => env('MODELER_LLM_GLM_MODEL_ID', 'glm-5'),
        'LLM_OPENAI_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_OPENAI_CHAT_COMPLETIONS_URL', ''),
        'LLM_OPENAI_MODEL_ID' => env('MODELER_LLM_OPENAI_MODEL_ID', ''),
        'LLM_CLAUDE_CHAT_COMPLETIONS_URL' => env('MODELER_LLM_CLAUDE_CHAT_COMPLETIONS_URL', ''),
        'LLM_CLAUDE_MODEL_ID' => env('MODELER_LLM_CLAUDE_MODEL_ID', ''),
        'LLM_CLAUDE_VERSION' => env('MODELER_LLM_CLAUDE_VERSION', ''),
        'LLM_CLAUDE_MAX_TOKENS' => (int) env('MODELER_LLM_CLAUDE_MAX_TOKENS', 1024),
    ],
    'morph_map' => [
        ...($generatedConfig['morph_map'] ?? []),
    ],

    'seeders' => [
        ...($generatedConfig['seeders'] ?? []),
    ],

    'migration_dirs' => [
        ...($generatedConfig['migration_dirs'] ?? ['database/migrations']),
    ],

    'fake_quantity' => (int) env('MODELER_FAKE_QUANTITY', 25),
];
