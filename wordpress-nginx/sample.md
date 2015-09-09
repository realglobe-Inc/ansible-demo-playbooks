# PC上での手順

## 目次

### Ansibleを実行する

1. VMを作成する
2. Ansible実行マシンにSSH接続する
3. 実行マシン上にAnsibleをインストール
4. Playbookをダウンロード
5. WordpressサーバーへのSSH接続設定
6. インベントリの作成
7. ansible-playbookを実行

### playbookを編集する

1. DBサーバーを作成する
  * 新しくDB用サーバーのVMを作成する
  * コントロールマシンからSSH接続できることを確認する
2. インベントリにグループを追加する
  * playbookを編集する
  * site.ymlを編集する
  * mysql roleを編集する
3. wordpress roleを編集する

# Ansibleを実行する

コントロールマシンにSSHログインし、Ansibleをインストールしてplaybookを実行する

## 1. VMを作成する

* Ansible実行マシン用VMを作成する
  * イメージは「CentOS 7.1 64-bit」を使用
  * 仮想マシン名は「ansible-machine」

* Wordpressサーバー用VMを作成する
  * イメージは「CentOS 6.6 64-bit」を使用
  * 仮想マシン名は「wordpress-server」

:warning: IDCFコントロール・パネル操作方法の詳細は、「IDCFクラウド上での手順」スライドを参照

## 2. Ansible実行マシンにSSH接続する

#### :small_blue_diamond: Mac、Linuxの場合

1. ターミナルを開く
2. VM作成時に使った秘密鍵のパーミッションを600に変更する

    ```chmod 600 ssh_private_key```

3. Ansible実行マシンにSSH接続する

    ```ssh root@xxx.xxx.xxx.xxx -i ssh_private_key```

#### :small_blue_diamond: Windowsの場合（Tera term使用時）

1. Tera termを起動する
2. ホストにはAnsible実行マシンのIPアドレスを入力
3. ユーザー名は「root」、秘密鍵にはVM作成時に使った秘密鍵を指定

## 3．実行マシン上にAnsibleをインストール

### yumでAnsibleをインストールする

1. エディタをインストールする
  * vimやemacsに不慣れな方には nano がオススメです

    ```sh
    yum install nano -y
    echo 'set const' >> ~/.nanorc
    ```

2. yumリポジトリにEPELを追加する

    ```yum install epel-release -y```

3. Ansibleをインストール

    ```yum install ansible -y```

4. ansibleコマンドが使用できることを確認する

    ```sh
    ansible[Tab][Tab]
    ansible    ansible-doc    ...    ansible-vault
    ```

## 4．playbookをダウンロード

今回のハンズオンで使用するplaybookをダウンロードする

1. GitHubの公開リポジトリからgit cloneする

    ```git clone https://github.com/realglobe-Inc/ansible-demo-playbooks.git```

2. cdコマンドを使用しディレクトリを移動する

    ```cd ansible-demo-playbooks/wordpress-nginx/```

## 5. WordpressサーバーへのSSH接続設定

Ansible実行マシンからWordpressサーバーにSSH接続できるようにする

1. VM作成時に指定した秘密鍵をAnsible実行マシンにコピペする

    ```nano ~/.ssh/id_rsa```

2. 秘密鍵のパーミッションを600に変更する

    ```chmod 600 ~/.ssh/id_rsa```

3. sshでWordpressサーバーに接続できることを確認する

    ```ssh xxx.xxx.xxx.xxx```

4. Wordpressサーバーからログアウトする

    ``` exit ```

## 6. インベントリ作成

今回は`hosts`という名前でインベントリファイルを作成する

1. リポジトリ内に`hosts.example`というファイルがあるので、これをコピーして使用する

    ```cp hosts.example hosts```

2. エディタで`hosts`を開く

    ```nano hosts```

3. 以下のように書き換える（xxx.xxx.xxx.xxxはWordpressサーバーのIPアドレス）

    ```ini
    [wordpress-server]
    xxx.xxx.xxx.xxx
    ```

## 7. ansible-playbook実行

ansible-playbookコマンドを実行する

1. wordpressをインストールするplaybookを実行

    ```ansible-playbook site.yml -i hosts```

2. ブラウザからアクセスして、Wordpressインストール画面が表示されることを確認

3. wordpressをアンインストールするplaybookを実行

    ```ansible-playbook clean.yml -i hosts```

4. Wordpressにアクセスできなくなっていることを確認

# playbookを編集する

DBを別サーバーに分けてみる

### 変更点の概要

* プレイブックを2台構成のシステムに分割する
  * アプリケーションサーバー
  * DBサーバー

* CentOS 7.1 に対応させる
  * CentOS 7系では、mysqlではなくmariadbがデフォルト
    * プレイブックに修正が必要

## 8. DBサーバーを作成する

1. 新しくDBサーバー用のVMを作成する
  * OSはCentOS7.1で作成
  * SSH KeyはWebサーバーと同じものを使用する
  * ローカルネットワーク内からしかアクセスしないので、IPアドレスの設定は必要なし

2. Ansible実行マシンからssh接続できることを確認する
  * Wordpressサーバーと同じSSH Keyを使用しているので、設定の変更なしにSSH接続できる

    ```sh
    ssh xxx.xxx.xxx.xxx
    exit
    ```

## 9. インベントリにグループを追加する

インベントリを書き換え、wordpress-dbグループを追加する

* エディタで`hosts`を開き、以下のように書き換える

    ```ini
    [wordpress-server]
    xxx.xxx.xxx.xxx

    [wordpress-db]
    xxx.xxx.xxx.xxx
    ```

## 10. playbookを編集する

### site.ymlを編集し、Webサーバーの設定手順(web.yml)とDBサーバーの設定手順(db.yml)に分割する。

* site.yml

    ```yaml
    - hosts: all
    - include: db.yml
    - include: web.yml
    ```

* db.yml

    ```yaml
    - name: MySQLをインストール
      hosts: wordpress-db
      sudo: yes
      roles:
        - common
        - mysql
    ```

* web.yml

    ```yaml
    - name: Wordpress, Nginx, PHP-FPMをインストール
      hosts: wordpress-server
      sudo: yes
      roles:
        - common
        - nginx
        - php-fpm
        - wordpress
    ```

### MySQLではなくMariaDBを使うように、プレイブックの該当部分を書き換える

:warning: 指定された箇所のみを編集

* `roles/mysql/tasks/main.yml`を編集する

    ```yaml
    - name: mariadb-serverをインストール
      yum: name=mariadb-server state=present

    ...

    - name: mariadb設定ファイルを設置
      template: src=my.cnf.j2 dest=/etc/my.cnf
      notify:
        - mariadb再起動
    - name: mariadbを起動
      service: name=mariadb state=started enabled=true
    ```

* `roles/mysql/handlers/main.yml`を編集する

    ```yaml
    - name: mariadb再起動
      service: name=mariadb state=restarted
    ```

### roles/mysql/templates/my.cnf.j2を書き換え、ログやプロセスIDを保存する先を変更する

* そのままだと保存先のディレクトリに書き込み権限がないため、DB起動時にファイルが作成できずエラーになる

    ```ini
    [mysqld_safe]
    log-error=/var/log/mariadb/mariadb.log
    pid-file=/var/run/mariadb/mariadb.pid
    ```

### mysql操作系のタスクの実行場所を移動する

* 以下2タスクを`roles/wordpress/tasks/main.yml`から`roles/mysql/tasks/main.yml`の最後尾に移動する

    ```yaml
    - name: Wordpress用データベースを作成
      mysql_db: name={{ wp_db_name }} state=present
    - name: Wordpress用データベースユーザーを作成
      mysql_user: name={{ wp_db_user }}
        password={{ wp_db_password }}
        priv={{ wp_db_name }}.*:ALL
        host={{ hostvars[groups['wordpress-server'][0]].ansible_default_ipv4.address }}
        state=present
    ```

### WordpressがDBサーバーに接続できるように、設定ファイルを修正する

* `roles/wordpress/templates/wp-config.php`を修正する

    ```php
    /** MySQL hostname */
    define('DB_HOST', '{{ hostvars[groups['wordpress-db'][0]].ansible_default_ipv4.address }}');
    ```

### ansible-playbookを実行して、ブラウザからアクセスし、wordpressをセットアップする
