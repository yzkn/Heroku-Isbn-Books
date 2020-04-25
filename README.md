# Laravel-Isbn

---

## 設置方法

### 01. Herokuアカウントを作成

https://signup.heroku.com/login

### 02. Herokuアプリケーションを作成

https://dashboard.heroku.com/new-app

### 03. Heroku CLIをインストール

https://cli-assets.heroku.com/heroku-x64.exe

### 04. リポジトリの clone

```sh
# clone先ディレクトリは任意(apacheのドキュメントルート配下は避ける)
$ cd /PATH/TO/LARAVELISBN

# フォルダ名は任意(アプリ名に合わせるか、複数設置したときに区別しやすいもの)
$ git clone https://github.com/YA-androidapp/Heroku-Isbn-Comics books
```

### 05. clone したリポジトリのディレクトリで composer を実行

```sh
$ cd books
$ composer install
```

### 06. データベースを設定

#### 06.1. PoetgreSQLアドオンを追加

HerokuアプリケーションのResourceタブで `Heroku Postgres` を検索して追加

#### 06.2. PoetgreSQL で、データベースとユーザーを作成

[DBeaver](https://dbeaver.io/) 推奨

### 07. 環境変数を設定

#### 07.1. データベースの接続情報を設定

HerokuアプリケーションのSettingsからReveal Config Varsを開く。
Heroku PostgresアドオンのSettingsのView Credentialsを参考に以下の環境変数を設定

* DATABASE_URL
* DB_CONNECTION	pgsql
* DB_DATABASE
* DB_HOST
* DB_PASSWORD
* DB_USERNAME
* DEBUGBAR_ENABLED	true
* LOG_CHANNEL	errorlog

#### 07.2. データベースの動作確認

```ps
$ heroku pg:psql DATABASE_URL
```

#### 07.3. APP_KEYを設定

```ps
$ heroku config:set APP_KEY=$(php artisan key:generate --show)
```

### 08. マイグレーションの実行

```sh
$ heroku run php artisan migrate
```

既存DBからデータをインポートした場合はincrementをきちんと動作させるために以下のSQLを実行

```sql
SELECT setval('users_id_seq', coalesce((SELECT MAX(id)+1 FROM users), 1), false);
SELECT setval('books_id_seq', coalesce((SELECT MAX(id)+1 FROM books), 1), false);
```

### 09. SSL対応

* `TrustProxies.php` に以下を追記

```php
    protected $proxies = '**';
```

---

Copyright &copy; 2020 YA-androidapp(https://github.com/YA-androidapp) All rights reserved.
