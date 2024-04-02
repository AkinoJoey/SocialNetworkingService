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

## これからの改善点、拡張案
