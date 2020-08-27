# Flywheel3 Stream library for old php versions

[fw3/streams: Flywheel3 stream library](https://github.com/fw3/streams)で公開しているStreamFilterをPHP5.3.3以降でも動作するようにした、実用向けのストリームフィルタです。

お手軽簡単、今すぐに利用したい方は [定などの注意点](#定などの注意点) 、 [使い方](#使い方) を参照し、ライブラリを導入後、 [応用：初期化設定もライブラリに任せた実装](#応用：初期化設定もライブラリに任せた実装) にある実装を試してみてください。

PHP7.2.0未満への対応が不要な場合、 [fw3/streams](https://github.com/fw3/streams) を使用してください。

## 対象バージョンおよび動作確認バージョン

対象バージョン：PHP5.3.3以降

### 動作確認バージョン

- **5.3.3**
- 5.3.4
- 5.3.5
- 5.3.6
- 5.3.7
- 5.3.8
- **5.3.9**
- 5.4.16
- 5.4.39
- **5.4.45**
- **5.5.38**
- **5.6.40**
- **7.0.33**
- **7.1.33**
- **7.2.33**
- **7.3.21**
- **7.4.0**
- **7.4.9**
- **8.0.0beta2**

5.3.3以降の各マイナーバージョンの最新バージョンとロカールの取り扱いが変わるタイミングでのバージョンに対して動作確認を行っています。

### 設定などの注意点

#### Windows (php7.2.0未満)

php.iniの次の行のコメントを除去してください。

```diff
- ; extension_dir = "ext"
+ extension_dir = "ext"
```

```diff
- ;extension=php_mbstring.dll
+ extension=php_mbstring.dll
```

#### Windows (php7.2.0以上)

php.iniの次の行のコメントを除去してください。

```diff
- ; extension_dir = "ext"
+ extension_dir = "ext"
```

```diff
- ;extension=mbstring
+ extension=mbstring
```

#### Linux系 (パッケージマネージャ使用)

各種パッケージマネージャで`php-mbstring`またはそれに類するものをインストールしてください。

#### Linux系 (phpenv使用)

`default_configure_options`または各definitionに次の一つを追加してください。

```
--enable-mbstring
```

#### Linux系 (ソースコードからビルド)

configureオプションに次の一つを追加してください。
詳細は[PHP マニュアル 関数リファレンス 自然言語および文字エンコーディング マルチバイト文字列 インストール/設定](https://www.php.net/manual/ja/mbstring.installation.php)を参照してください。

```
--enable-mbstring
```

## 使い方

### 1 . インストール

#### composerを使用できる環境の場合

次のコマンドを実行し、インストールしてください。

`composer require fw3_for_old/streams`

#### composerを使用できない環境の場合

[Download ZIP](https://github.com/fw3-for-old/streams/archive/master.zip)よりzipファイルをダウンロードし、任意のディレクトリにコピーしてください。

使用対象となる処理より前に`require_once sprintf('%s/src/filters_require_once.php', $path_to_copy_dir);`として`src/filters_require_once.php`を読み込むようにしてください。

### 2. ストリームフィルタへの登録を行います

```php
<?php

use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;

//----------------------------------------------
// フィルタ登録
//----------------------------------------------
// 引数を使用することでお好きなフィルタ名を設定することができます。
//
// StreamFilterSpec::registerConvertEncodingFilter(StreamFilterConvertEncodingSpec::DEFAULT_FILTER_NAME);
// StreamFilterSpec::registerConvertLinefeedFilter(StreamFilterConvertLinefeedSpec::DEFAULT_FILTER_NAME);
//----------------------------------------------
StreamFilterSpec::registerConvertEncodingFilter();
StreamFilterSpec::registerConvertLinefeedFilter();
```

### 3. CSV出力

実行前にロカールの設定と、実行後にロカールの設定を戻すことを**必ず**行ってください。

```php
<?php

use fw3_for_old\streams\filters\ConvertEncodingFilter;
use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

// 設定
$path_to_csv    = '';       // 出力CSVファイルパス
$rows           = array(    // 出力する内容を持つ二次元配列
    array(),
);

// 実行時のロカールと代替文字設定を先行して設定します
ConvertEncodingFilter::startChangeLocale();
ConvertEncodingFilter::startChangeSubstituteCharacter();

// フィルタ設定の構築：書き込み用として UTF-8 => SJIS-win、任意の行末改行コード => CRLF に変換するストリームフィルタ設定を構築する。
$spec   = StreamFilterSpec::resource($path_to_csv)->write(array(
    StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
    StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
));

// CSVファイルの出力
$fp     = \fopen($spec->build(), 'wb');
foreach ($rows as $row) {
    \fputcsv($fp, $row);
}
\fclose($fp);

// 実行時のロカールと代替文字設定を元に戻します
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

### 4. CSV入力

```php
<?php

use fw3_for_old\streams\filters\ConvertEncodingFilter;
use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;

// 設定
$path_to_csv    = '';       // 入力CSVファイルパス

// 実行時のロカールと代替文字設定を先行して設定します
ConvertEncodingFilter::startChangeLocale();
ConvertEncodingFilter::startChangeSubstituteCharacter();

// フィルタ設定の構築：読み込み用として 任意のエンコーディング => UTF-8 に変換するストリームフィルタ設定を構築する。
$spec   = StreamFilterSpec::resource($path_to_csv)->read([
    StreamFilterConvertEncodingSpec::toUtf8()->fromDefault(),
]);

$fp     = fopen($spec->build(), 'rb');
$rows   = array();
while ($row = fgetcsv($fp)) {
    $rows[] = $row;
}
fclose($fp);

// 実行時のロカールと代替文字設定を元に戻します
ConvertEncodingFilter::endChangeSubstituteCharacter();
ConvertEncodingFilter::endChangeLocale();
```

#### 応用：初期化設定もライブラリに任せた実装

フィルタ登録やロカールと代替文字の設定と実行後のリストアなど、ボイラープレートとなりがちな処理をライブラリに任せて実行することもできます。

##### 無難なCSV入出力

```php
<?php

use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//==============================================
// 設定
//==============================================
$rows   = array(array());   // データ

$path_to_csv    = '';   // CSVファイルのパスを設定して下さい

//----------------------------------------------
// 一括即時実行
//----------------------------------------------
// フィルタ登録、ロカールと代替文字の設定と実行後のリストアも包括して実行します。
// コールバックの実行中に例外が発生してもロカールと代替文字のリストアは実行されます。
//----------------------------------------------
$result = StreamFilterSpec::decorateForCsv(function () use ($path_to_csv, $rows) {
    //==============================================
    // 書き込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->write(array(
        StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
        StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
    ));

    // CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
    $fp     = \fopen($spec->build(), 'r+b');
    foreach ($rows as $row) {
        \fputcsv($fp, $row);
    }
    \fclose($fp);

    //==============================================
    // 読み込み
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resource($path_to_csv)->read(array(
        StreamFilterConvertEncodingSpec::toUtf8()->fromSjisWin(),
    ));

    // UTF-8としてCSV読み込みを行う（\SplFileObjectでも使用できます。）
    $rows   = array();
    $fp     = \fopen($spec->build(), 'r+b');
    for (;($row = \fgetcsv($fp, 1024)) !== FALSE;$rows[] = $row);
    \fclose($fp);

    return $rows;
});
```

##### HTTP経由でのCSVダウンロード

```php
<?php

use fw3_for_old\streams\filters\utilitys\StreamFilterSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertEncodingSpec;
use fw3_for_old\streams\filters\utilitys\specs\StreamFilterConvertLinefeedSpec;

//----------------------------------------------
// 一括即時実行
//----------------------------------------------
// フィルタ登録、ロカールと代替文字の設定と実行後のリストアも包括して実行します。
// コールバックの実行中に例外が発生してもロカールと代替文字のリストアは実行されます。
//----------------------------------------------
StreamFilterSpec::decorateForCsv(function () {
    //==============================================
    // 例：PDOで取得したデータをそのままCSVとしてDLさせてみる
    //==============================================
    // フィルタの設定
    $spec   = StreamFilterSpec::resourceOutput()->write(array(
        StreamFilterConvertEncodingSpec::toSjisWin()->fromUtf8(),
        StreamFilterConvertLinefeedSpec::toCrLf()->fromAll(),
    ));

    // 仮のDB処理：実際のDB処理に置き換えてください
    $pdo    = new \PDO('spec to dsn');
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    $stmt   = $pdo->prepare('SELECT * FROM table');
    $stmt->execute();

    // 仮のHTTP Response Header
    \header('Content-Type: application/octet-stream');
    \header('Content-Disposition: attachment; filename=fw3-sample.csv');

    // CP932、行末の改行コードCRLFとしてCSV書き込みを行う（\SplFileObjectでも使用できます。）
    $fp     = \fopen($spec->build(), 'wb');
    foreach ($stmt as $row) {
        \fputcsv($fp, $row);
    }
    \fclose($fp);
});
```

## ユニットテスト

次の形で`tests/test.php`を実行します。

```php
php tests/test.php
```
