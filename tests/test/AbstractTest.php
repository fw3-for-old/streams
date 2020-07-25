<?php
/**  ___ _  _ ___  __         ___
 *  | _ | || | _ \/ _|___ _ _| ____ _____ _ _ _  _ ___ _ _  ___
 *  |  _| __ |  _|  _/ _ | '_| _|\ V / -_| '_| || / _ | ' \/ -_)
 *  |_| |_||_|_| |_| \___|_| |___|\_/\___|_|  \_, \___|_||_\___|
 *                                            |__/
 *
* Flywheel3: the inertia php framework for old php versions
 *
 * @category    test
 * @package     sample
 * @author      akira wakaba <wakabadou@gmail.com>
 * @copyright   Copyright (c) @2019  Wakabadou (http://www.wakabadou.net/) / Project ICKX (https://ickx.jp/). All rights reserved.
 * @license     http://opensource.org/licenses/MIT The MIT License.
 *              This software is released under the MIT License.
 * @varsion     1.0.0
 */

/**
 * 抽象テスト実施クラス
 */
abstract class AbstractTest implements \TestInterface
{
    /**
     * @var array   テストログ
     */
    protected $logs = array(
        'success'   => array(),
        'failed'    => array(),
    );

    /**
     * 値を型判定付きでアサーションします。
     *
     * @param   array   $expected   予想される値
     * @param   array   $actual     実際の値
     */
    protected function assertSame($expected, $actual)
    {
        $this->log($expected === $actual, $expected, $actual);
    }

    /**
     * 与えられたストリームで書き込み時のストリームフィルタをアサーションします。
     *
     * @param   string  $expected       予想される値
     * @param   string  $value          実行する値
     * @param   array   $steram_wrapper ストリームコンテキスト
     */
    protected function assertWriteStreamFilterSame($expected, $value, $steram_wrapper)
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');
        $length = @\fwrite($fp, $value);

        @\rewind($fp);

        $actual = @\fread($fp, $length);

        @\fclose($fp);

        $status = $expected === $actual;

        $str_expected   = \str_replace(array("\r\n", "\r", "\n"), array("\\r\\n", "\\r", "\\n"), $expected);
        $str_actual     = \str_replace(array("\r\n", "\r", "\n"), array("\\r\\n", "\\r", "\\n"), $actual);

        $this->log($status, $str_expected, $str_actual);
    }

    /**
     * 与えられたストリームで書き込み時のストリームフィルタが異なる結果になる事をアサーションします。
     *
     * @param   string  $expected       予想される値
     * @param   string  $value          実行する値
     * @param   array   $steram_wrapper ストリームコンテキスト
     */
    protected function assertWriteStreamFilterNotSame($expected, $value, $steram_wrapper)
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');
        $length = @\fwrite($fp, $value);

        @\rewind($fp);

        $actual = @\fread($fp, $length);

        @\fclose($fp);

        $status = $expected !== $actual;

        $str_expected   = \str_replace(array("\r\n", "\r", "\n"), array("\\r\\n", "\\r", "\\n"), $expected);
        $str_actual     = \str_replace(array("\r\n", "\r", "\n"), array("\\r\\n", "\\r", "\\n"), $actual);

        $this->log($status, $str_expected, $str_actual);
    }

    /**
     * 与えられたストリームでCSV入力をアサーションします。
     *
     * @param   array   $expected           予想される値
     * @param   string  $csv_text           実行する値
     * @param   int     $stream_chunk_size  ストリームラッパーのチャンクサイズ
     * @param   array   $steram_wrapper     ストリームコンテキスト
     */
    protected function assertCsvInputStreamFilterSame($expected, $csv_text, $stream_chunk_size, $steram_wrapper)
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');

        @\fwrite($fp, $csv_text);

        @\rewind($fp);

        if (function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($fp, $stream_chunk_size);
        }

        $actual = array();
        while ($row = \fgetcsv($fp)) {
            $actual[]   = $row;
        }

        @\fclose($fp);

        $status = $expected === $actual;

        $this->log($status, $expected, $actual);
    }

    /**
     * 与えられたストリームでCSV出力をアサーションします。
     *
     * @param   string  $expected           予想される値
     * @param   array   $csv_data           実行する値
     * @param   int     $stream_chunk_size  ストリームラッパーのチャンクサイズ
     * @param   array   $steram_wrapper     ストリームコンテキスト
     */
    protected function assertCsvOutputStreamFilterSame($expected, $csv_data, $stream_chunk_size, $steram_wrapper)
    {
        $steram_wrapper['resource']   = 'php://temp';

        $write_stream   = $this->convertSteramWrapper($steram_wrapper);

        $fp     = @\fopen($write_stream, 'ab');

        if (function_exists('stream_set_chunk_size')) {
            \stream_set_chunk_size($fp, $stream_chunk_size);
        }

        if (function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($fp, $stream_chunk_size);
        }

        foreach ($csv_data as $data) {
            @\fputcsv($fp, $data);
        }

        @\rewind($fp);

        $actual = '';
        while ($row = \fread($fp, 1024)) {
            $actual .= $row;
        }

        @\fclose($fp);

        $status = $expected === $actual;

        $this->log($status, $expected, $actual);
    }

    /**
     * 例外をアサーションします。
     *
     * @param   string      $expected   予想される値
     * @param   \Exception  $e          実際の例外
     * @return boolean
     */
    protected function assertException($expected, $e)
    {
        $actual = $e->getMessage();
        $status = $expected === $actual;

        $this->log($status, $expected, $actual);
    }

    /**
     * アサーションの実行内容をログに保存します。
     *
     * @param   bool    $status     実行時の検証結果
     * @param   mixed   $expected   予想される値
     * @param   mixed   $actual     実際の値
     */
    protected function log($status, $expected, $actual)
    {
        $backtrace          = \debug_backtrace();
        $backtrace_detail   = \sprintf(
            '%s%s%s() in line %d',
            $backtrace[2]['class'],
            $backtrace[2]['type'],
            $backtrace[2]['function'],
            $backtrace[1]['line']
        );

        $key = $status ? 'success' : 'failed';
        $this->logs[$key][] = array(
            'backtrace' => $backtrace_detail,
            'actual'    => $actual,
            'expected'  => $expected,
        );
    }

    /**
     * Stream Wrapper設定を文字列表現に変換します。
     *
     * @param   array   $steram_wrapper ストリームラッパー設定
     * @return  string  ストリームラッパー設定
     */
    protected function convertSteramWrapper($steram_wrapper)
    {
        $stack  = array();
        foreach ($steram_wrapper as $key => $context) {
            $stack[]    = \sprintf('%s=%s', $key, \implode('|', (array) $context));
        }

        return \sprintf('php://filter/%s', \implode('/', $stack));
    }

    /**
     * 現在までに保存されたログを返します。
     *
     * @return  array   現在までに保存されたログ
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * セットアップ
     */
    public function setup()
    {
    }

    /**
     * テスト一括実行器
     */
    public function __invoke()
    {
        $rc = new \ReflectionClass($this);

        foreach ($rc->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (\substr($method->name, -4) === 'Test') {
                $method->invoke($this);
            }
        }
    }

    /**
     * ティアダウン
     */
    public function teardown()
    {
    }
}
