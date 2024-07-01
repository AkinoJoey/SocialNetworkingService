# ten
テキストや画像、動画などの投稿、プライベートチャットを楽しめるSNSウェブアプリケーション。  
アプリ名に「ten」という単語を使ったのは、人々が交わる（クロス）というイメージを表現するためです。そのイメージに合うのが、「t」から始まり、漢字で「十」という意味を持つ「ten」という単語でした。

## URL
[https://ten.yuki-gakiya.com](https://ten.yuki-gakiya.com)

## Demo

## 概要
このSNSウェブアプリケーションは、テキストや画像などの投稿、相互フォロー、プライベートチャットなど、さまざまな機能を楽しむことができるものです。また、パソコンやタブレット、スマートフォンなど、どの端末でも使いやすいように、レスポンシブ対応のUIを採用しています。

## 主な機能


## 機能一覧
### アカウント
- アカウント作成
- Eメール認証
- ログイン
- プロフィール画像、年齢、場所、自己紹介などのプロフィール作成
- アカウントの削除
- パスワードのリセット

### 投稿
- テキストの投稿
- 画像の投稿
- 動画の投稿
- 投稿の削除
- 日付・時間指定による予約投稿

### ユーザーとのコミュニケーション
- ダイレクトメッセージ
- フォロー中、トレンド別のタイムライン表示
- ユーザー名、アカウント名でのユーザー検索
- 投稿に対するいいね機能
- 投稿への返信

### 通知
- 自分の投稿への返信
- 自分の投稿へのいいね
- フォロー
- ダイレクトメッセージの受信

## 作成の経緯
このSNSウェブアプリケーションの制作は、これまでの学習やプロジェクト作成を通じて得た知識とスキルを活かし、より大規模なプロジェクトに取り組むことを目指して始めました。独自のマイクロフレームワークを使用し、ゼロからアプリケーションを構築することで、将来的にどのようなバックエンドフレームワークを学習する際にも役立つスキルセットを身につけることを意図しています。

MVC（Model-View-Controller）アーキテクチャを採用し、データベースの管理にはマイグレーションベースを採用しました。また、データベースへのアクセスにはDAO（Data Access Object）を利用しています。

特にセキュリティへの取り組みには重点を置き、以下のセキュリティ対策を実装しました。

- セッションベースのユーザー認証
- ユーザー入力のバリデーション
- プリペアドステートメントの使用
- テキストの暗号化
- CSRF（Cross-Site Request Forgery）対策
- 署名付きURLの発行
- Eメールの検証

これらの実装についての詳細は、後述する「こだわった点」で解説します。

## 使用技術
### フロントエンド
| 項目                | 内容                         |
|---------------------|------------------------------|
| 使用言語            | HTML, CSS, Javascript        |
| CSSフレームワーク    | Tailwind     |
| Tailwindライブラリ   | flowbite, daisyUI     |
| 日付選択ライブラリ   | Flatpickr     |
| リアルタイム通信    | WebSocket    |
| パッケージ管理    | npm    |
| モジュールバンドラー    | webpack    |

### バックエンド
| 項目                | 内容                         |
|---------------------|------------------------------|
| 使用言語            | PHP                          |
| データベース        | MySQL                        |
| ジョブ管理ツール     | cron                      |
| 単体テスト     　　| PHPUnit                |
| メール送信     　　| PHPMailer                |
| リアルタイム通信    | Ratchet                        |
| プロセス制御システム  |  Supervisor                    |
| 画像編集            | ImageMagick                  |
| 動画編集            | ffmpeg                  |
| 日付操作            | Carbon                      |
| ダミーデータ        | Faker                       |
| ダミー画像        | Pixabay API                       |
| パッケージ管理      | Composer           　        |
| オートローダー      | Composer           　        |
| Webサーバー         | NGINX                        |
| サーバー            | Amazon EC2                   |
| SSL/TLS証明書更新    | Certbot                      |

## 期間
2024年1月18日から約2か月半かけて開発しました。

## こだわった点
### セキュリティ対策
安全なアプリケーションを開発するために、大きく以下の3つのセキュリティ対策を実装しました。

- 署名付きURLによる検証
- ミドルウェアによるアクセス制限
- ユーザー入力の検証

#### 署名付きURLによる検証
ユーザーが入力したEメールが有効かどうかを確認するEメール認証機能を導入しました。
これにより不正なアカウントの作成を防止します。

サーバーは[アカウント作成ページ](https://ten.yuki-gakiya.com/signup)で入力された情報を受け取ると、署名付きURLを作成し、入力されたEメール宛に送信します。
ユーザーが受信した署名付きURLを開くと、Eメールが有効であるとみなされアカウントが作成されます。

URLの署名には、サーバーサイドのシークレットキーとHMAC SHA256メッセージハッシュアルゴリズムを使用しました。
署名付きURLにはユーザーID、ハッシュ化されたEメール、有効期限を含めました。

サーバーが署名URLへのリクエストを受け取ると、現在のURLをシークレットキーでハッシュし、URLに埋め込まれた署名と比較して署名の有効性を検証します。
署名が有効であることが確認されると、ユーザーのアカウント作成が成功し、ログインが可能になります。

このフローを以下の図に示します。

パスワードリセット機能も同様の署名付きURLを使用して実装しています。
さらに、パスワードリセットの場合は、リクエストしたユーザーのIDとトークンを管理するためのパスワードリセットテーブルを使用しています。

### ミドルウェアによるアクセス制限
各ページへのアクセスはミドルウェアによって制限されています。ほとんどのページは、ログイン状態かつEメール認証済みのユーザーのみが閲覧可能です。

例えば未ログインの状態で、ログインが必要なページ([https://ten.yuki-gakiya.com/messages](https://ten.yuki-gakiya.com/messages)など)に、アクセスすると下記の画像のように、アラートが表示されログインページにリダイレクトされます。
これは[AuthenticatedMiddleware](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/middleware/AuthenticatedMiddleware.php)によって実行されます。  

このように、ミドルウェアはユーザーのログイン状態やEメール認証の有無などを判断し、特定のページや機能へのアクセスを許可します。

ユーザー認証にはセッションベースの方法を採用しています。ログインページで正しいEメールとパスワードが入力されると、セッションデータにユーザーIDが付与され、アクセスが許可されます。セッションが有効な限り、ユーザーはログイン状態を維持します。

各リクエストでは、クライアントから送信されたセッションデータのみをチェックすればよく、サーバー側での認証は不要です。

CSRF攻撃への対策として、ミドルウェアとCSRFトークンを利用しています。サーバーはCSRFトークンの有効性をチェックし、無効な場合はリクエストを拒否します。
CSRFトークンの生成とCSRFトークンの検証は[CSRFMiddleware](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/middleware/CSRFMiddleware.php)で行います。

Eメール検証機能やパスワードリセット機能に使用する署名付きURLの確認にもミドルウェアを使用しています。
[SignatureValidationMiddleware](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/middleware/SignatureValidationMiddleware.php)ではURLの署名の有効性や有効期限のチェックを行います。


### ユーザー入力の検証
このSNSウェブアプリケーションでは、アカウント作成やログイン、プロフィール編集、投稿など、ユーザーが入力する機会が多くあります。ユーザーが入力した情報はすべて厳密なバリデーションが行われており、誤ったデータ入力によるバグを防止しています。

バリデーション関数は[ValidationHelperクラス](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/helpers/ValidationHelper.php)で管理しています。また、バリデーション関数が適切に機能していることを確認するために、PHPUnitを使用してテストを行っています。

テストには[ValidationHelperTestクラス](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/test/helpers/ValidationHelperTest.php)と[ValidationHelperDataProviderクラス](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/test/helpers/ValidationHelperDataProvider.php)を使用しています。

さらに、SQLやスクリプト、コマンドなどの注入攻撃に対する対策も実施しています。クエリの実行にはプリペアドステートメントを使用し、HTMLのレンダリング時にはhtmlspecialchars関数を使用してサニタイズを行っています。

また、機密データを保護するために適切なタイミングでテキストの暗号化やハッシュ化を行っています。例えば、アカウント作成に使用されるパスワードは検証が必要であるため、password_hash関数を使用してハッシュ化しています。

また、ダイレクトメッセージなど、元のメッセージに戻す必要がある場合は、openssl_encrypt関数を使用して暗号化を行っています。暗号化に関する関数は[CipherHelperクラス](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/helpers/CipherHelper.php)で管理しています。

### cronによる定期スケジューリング
このSNSウェブアプリケーションでは、定期的な処理を実行するためにcronを活用しています。

まず、予約投稿機能を実現するために、[PostScheduled](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/commands/programs/PostScheduled.php)というコマンドを作成しました。このコマンドは、スケジュールされた投稿データの中で現在の時間を過ぎているデータのステータスをスケジューリング状態から公開状態に変更します。そして、このコマンドを実行するためのスクリプト[post_scheduled.php](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/cron/post_scheduled.php)をcronで毎分実行しています。

また、データシーディングも定期的に行っています。このSNSウェブアプリケーションでは、実際の利用状況を模倣するために、数千人のユーザーがサービスを利用しているかのような状況を再現するためにデータシーディングを行っています。

まず、本番環境にアップした際に以下のデータを生成しました。
- 2000人のユーザー
- 6000の投稿
- 36000のいいね
- 18000のコメント
- 6000のコメントへの返信
- 3000のコメントへのいいね
- 1ユーザーあたり10 ~ 100のフォロー

その中で、50人をインフルエンサーとして設定し、フォローデータ生成時には、3分の1の確率でインフルエンサーをフォローするようにしました。

本番環境でアップ後は、実際のソーシャルメディアの動向を模倣するために、以下のデータ生成をランダムな時間でスケジューリングしています。
- 各ユーザーは毎日ランダムな内容の投稿を3つ行う
- 各ユーザーは毎日1つのランダムな投稿に返信を行う
- 各ユーザーはインフルエンサーアカウントの中から20の投稿にいいねをする

これらのデータ生成のスケジューリングにはMySQLのCREATE EVENTステートメントを使用しています。
また、データシーディングを定期的に実行するために、[SeedDao](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/commands/programs/SeedDao.php)というコマンドを作成し、それをcronで実行するためのスクリプト[seed_prototype.php](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/cron/seed_prototype.php)を作成しました。


### Web Socketの活用
このSNSアプリケーションでは非同期によるダイレクトメッセージ機能を実装するためにWebSocketを導入しました。

実装にあたって[Ratchet](https://github.com/ratchetphp/Ratchet)というライブラリを使用しています。
Ratchetが提供している[MessageComponentInterface](https://github.com/ratchetphp/Ratchet/blob/master/src/Ratchet/MessageComponentInterface.php)を実装して、[Chatクラス](https://github.com/AkinoJoey/SocialNetworkingService/blob/main/src/helpers/Chat.php)を作成しました。

チャットの全体像は以下の図のようになっています。


また、安全なダイレクトメッセージを実現するために、NGINXのリバースプロキシ機能を使ってWSS化も行っています。
## これからの改善点、拡張案
- 自分のコメントに返信された際の通知発行
- 画像の複数枚アップロード
- 投稿を検索する機能の実装