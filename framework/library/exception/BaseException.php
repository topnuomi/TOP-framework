<?php

namespace top\library\exception;

use Throwable;

class BaseException extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null, $file = '', $line = '') {
        if ($file)
            $this->file = $file;
        if ($line) {
            $this->line = $line;
        }
        parent::__construct($message, $code, $previous);
    }

    public function syntaxHighlight($code) {
        $code = preg_replace('/"(.*?)"/U', '"<span style="color: #007F00">$1</span>"', $code);
        $code = preg_replace('/(\s)\b(.*?)((\b|\s)\()/U', '$1<span style="color: #aa0">$2</span>$3', $code);
        $code = preg_replace('/(class)(.+)\s/', '<span style="color: #aa0;">$0</span>', $code);
        $code = preg_replace('/(\/\/)(.+)\s/', '<span style="color: #ccc;">$0</span>', $code);
        $code = preg_replace('/(\/\*.*?\*\/)/s', '<span style="color: #ccc;">$0</span>', $code);
        $code = preg_replace('/(\[|\{|\}|\])/', '<strong>$1</strong>', $code);
        $code = preg_replace('/(\(|\)|\-&gt;|\=&gt;)/', '<span style="color: #9c9c9c;">$1</span>', $code);
        $code = preg_replace('/(\$[a-zA-Z0-9_]+)/', '<span style="color: #ff5500">$1</span>', $code);
        $code = preg_replace('/\b(print|echo|new|function|return|true|false|namespace|use|class|extends|implements)\b/', '<span style="color: #86bbf1">$1</span>', $code);
        return $code;
    }

    /**
     * @param $filename
     * @param $line
     * @return string
     */
    private function readErrorFile($filename, $line) {
        $file = file($filename);
        $totalLine = count($file);
        $offset = 10;
        $offsetStart = $line - $offset;
        $offsetEnd = $line + $offset;
        $start = ($offsetStart <= 0) ? 2 : $offsetStart;
        $end = ($offsetEnd > $totalLine) ? $totalLine : $offsetEnd;
        $content = '';
        for ($i = $start; $i <= $end; $i++) {
            $content .= '<a ' . ($i == $line ? 'class="errLine"' : '') . '><span class="lineDetail"><b>' . $i . '</b>';
            $content .= $this->syntaxHighlight(htmlspecialchars($file[$i - 1])) . '</span></a>';
        }
        return '<pre>' . $content . '</pre>';
    }

    /**
     * @param \Exception|null $exception
     */
    public function handler($exception = null) {
        if (DEBUG) {
            $message = htmlspecialchars($exception->getMessage());
            $file = $exception->getFile();
            $line = $exception->getLine();
            $trace = $exception->getTraceAsString();
            $content = '<div class="codeblock">' . $this->readErrorFile($file, $line) . '</div>';
            $detail = '<div class="errorFileInfo word_wrap">位于 ' . $file . ' 第 ' . $line . ' 行</div>';
            $detail .= $content . '<div class="traceblock"><a id="showTrace" href="javascript:;">查看Trace信息</a>';
            $detail .= '<div id="traceDetail" style="display: none;"><pre class="word_wrap">' . $trace . '</pre></div></div>';
        } else {
            $message = '系统错误，请稍后重试。';
            $detail = '<span>请打开调试模式以查看详细信息。</span>';
        }
        $message = $this->translateMessage($message);
        $content = <<<EOF
<!DOCTYPE html>
<html lang="zh">
	<head>
		<meta charset="UTF-8" />
		<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
		<meta http-equiv="X-UA-Compatible" content="ie=edge" />
		<title>系统错误</title>
        <script src="/resource/jquery.min.js"></script>
	</head>
<body>
	<style>
		body,
		h1,
		h2,
		h3,
		h4,
		h5,
		h6,
		hr,
		p,
		blockquote,
		dl,
		dt,
		dd,
		ul,
		ol,
		li,
		pre,
		form,
		fieldset,
		legend,
		button,
		input,
		select,
		textarea,
		th,
		td {
			margin: 0;
			padding: 0
		}
		* {
			font-family: "Courier New";
			font-weight: bold;
			/*font-family: "Droid Sans Mono";*/
			-webkit-tap-highlight-color: rgba(0, 0, 0, 0);
		}
		a {
			text-decoration: none;
		}
		ul {
			list-style: none;
		}
		body {
			background: #eeeeee;
		}
		#showTrace {
            color: #9c9c9c;
            line-height: 25px;
		}
		.word_wrap {
            white-space: pre-wrap!important;
            word-wrap: break-word!important;
            *white-space:normal!important;
		}
		.msg_box {
			width: 82%;
			height: auto;
			margin: 20px auto;			
			background: #ffffff;
			padding: 15px;
			color: #9c9c9c;
			font-size: 12px;
		}
		.msg_box .mainMessage {
            color: #666666;
		    font-size: 14px;
		}
		.msg_box .detail {
            margin-top: 10px;
		}
		.detail .codeblock {
		    width: 100%;
		    height: auto;
            font-size: 12px;
		    margin: 10px auto;
		    border-radius: 5px;
		    border: 1px solid #EEEEEE;
            overflow-x: scroll;
		}
		.detail .codeblock .errLine {
		    background: #f7e6e6 !important;
		}
		.detail .codeblock a {
		    display: block;
		    width: 100%;
		    height: 22px;
		    color: #9c9c9c;
		    line-height: 22px;
		}
		.detail .codeblock a:hover{
		    background: #fbfbfb;
		}
		.detail .codeblock b {
		    display: inline-block;
		    width: 45px;
		    height: auto;
		    text-align: center;
		    margin-right: 5px;
		    border-right: 1px solid #EEEEEE;
		}
		.detail .errorFileInfo {
            width: 100%;
            line-height: 20px;
		}
		.detail .traceblock {
            width: 100%;
            height: auto;
            line-height: 16px;
		}
        .detail .traceblock pre {
            margin-top: 5px;
        }
		.version {
			width: 100%;
			height: 22px;
			color: #b9b9b9;
			font-size: 12px;
			text-align: center;
            line-height: 22px;
			margin: 0 auto;
			padding-bottom: 20px;
		}
	</style>
	<div class="msg_box">
		<p class="mainMessage">{$message}</p>
		<div class="detail">
            {$detail}
		</div>
	</div>
	<div class="version">TOP-framework</div>
    <script>
    	$(function() {
            var cb = $('.codeblock').width();
            var d = $('.lineDetail');
            var v = 0;
            for(var i = 0; i < d.length; i++) {
                if ($(d[i]).width() > v) {
                    v = $(d[i]).width();
                }
            }
            if (v < cb) {
                $('.codeblock').css({
                    'overflow-x': 'auto'
                });
            } else {
                $('.codeblock').find('a').css({
                    'width': v + 'px'
                });
            }
        });
    	$('#showTrace').click(function() {
    	    $('#traceDetail').toggle();
    	    var s = (($('#traceDetail').css('display') == 'block') ? '隐藏' : '查看') + 'Trace信息';
    	    $(this).html(s);
    	});
    </script>
</body>
</html>
EOF;
        header("HTTP/1.1 404 Not Found");
        echo $content;
        exit;
    }

    public function translateMessage($message) {
        $message = str_ireplace(
            ['Undefined variable', 'Undefined offset', 'Undefined index', 'syntax error,', 'Use of undefined constant'],
            ['未定义变量', '未定义数组下标', '未定义数组索引', '语法错误:', '使用未定义常量:'],
            $message
        );
        return $message;
    }
}