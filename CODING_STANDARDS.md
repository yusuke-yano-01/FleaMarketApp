# コーディングスタンダード

このプロジェクトでは、一貫性のあるコード品質を保つために以下のコーディングルールを適用しています。

## 命名規則

### PHP 命名規則

#### クラス名

-   **パスカルケース** (PascalCase) を使用
-   例: `UserController`, `ProductService`, `DatabaseSeeder`

#### メソッド名・関数名

-   **キャメルケース** (camelCase) を使用
-   例: `getUserData()`, `createProduct()`, `validateInput()`

#### 変数名

-   **キャメルケース** (camelCase) を使用
-   例: `userName`, `productList`, `isValid`

#### 定数名

-   **大文字のスネークケース** (UPPER_SNAKE_CASE) を使用
-   例: `MAX_RETRY_COUNT`, `DEFAULT_TIMEOUT`, `API_BASE_URL`

#### ファイル名

-   **スネークケース** (snake_case) を使用
-   例: `user_controller.php`, `product_service.php`, `auth_middleware.php`

### JavaScript/TypeScript 命名規則

#### クラス名

-   **パスカルケース** (PascalCase) を使用
-   例: `UserService`, `ProductManager`, `ApiClient`

#### 関数名・メソッド名

-   **キャメルケース** (camelCase) を使用
-   例: `getUserData()`, `createProduct()`, `handleSubmit()`

#### 変数名

-   **キャメルケース** (camelCase) を使用
-   例: `userName`, `productList`, `isLoading`

#### 定数名

-   **大文字のスネークケース** (UPPER_SNAKE_CASE) を使用
-   例: `MAX_RETRY_COUNT`, `DEFAULT_TIMEOUT`, `API_BASE_URL`

### CSS 命名規則

#### クラス名

-   **ケバブケース** (kebab-case) を使用
-   例: `user-profile`, `product-card`, `btn-primary`

#### ID 名

-   **ケバブケース** (kebab-case) を使用
-   例: `main-header`, `product-list`, `search-form`

## 基本ルール

### PHP

-   **PSR-12** 準拠
-   インデント: 4 スペース
-   文字エンコーディング: UTF-8
-   改行コード: LF (Unix)

### JavaScript/TypeScript

-   **ESLint** を使用
-   インデント: 4 スペース
-   セミコロン必須
-   シングルクォート使用

### CSS

-   **Prettier** を使用
-   インデント: 4 スペース
-   クラス名はケバブケース

## ファイル構成

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # コントローラー
│   │   ├── Middleware/      # ミドルウェア
│   │   └── Requests/        # フォームリクエスト
│   ├── Models/              # モデル
│   └── Services/            # ビジネスロジック
├── resources/
│   ├── views/               # Bladeテンプレート
│   ├── js/                  # JavaScript
│   └── css/                 # CSS
└── tests/                   # テストファイル
```

## 命名規則の例

### 良い例

```php
// クラス名（パスカルケース）
class UserAuthenticationService
{
    // 定数名（大文字スネークケース）
    private const MAX_LOGIN_ATTEMPTS = 3;
    private const DEFAULT_TIMEOUT = 30;

    // メソッド名（キャメルケース）
    public function authenticateUser(string $userName, string $password): bool
    {
        // 変数名（キャメルケース）
        $isValidUser = $this->validateCredentials($userName, $password);
        $loginAttempts = $this->getLoginAttempts($userName);

        return $isValidUser && $loginAttempts < self::MAX_LOGIN_ATTEMPTS;
    }
}
```

```javascript
// クラス名（パスカルケース）
class ProductManager {
    // 定数名（大文字スネークケース）
    static MAX_PRODUCTS_PER_PAGE = 20;
    static DEFAULT_SORT_ORDER = "name";

    // メソッド名（キャメルケース）
    async fetchProducts(pageNumber = 1) {
        // 変数名（キャメルケース）
        const productList = await this.apiClient.get("/products", {
            page: pageNumber,
            limit: ProductManager.MAX_PRODUCTS_PER_PAGE,
        });

        return this.formatProductData(productList);
    }
}
```

### 悪い例

```php
// ❌ クラス名がスネークケース
class user_authentication_service

// ❌ メソッド名がスネークケース
public function authenticate_user()

// ❌ 変数名がスネークケース
$user_name = 'john';

// ❌ 定数名がキャメルケース
private const maxLoginAttempts = 3;
```

## コメント

-   PHPDoc を使用
-   複雑なロジックには必ずコメントを記述
-   日本語コメントを推奨
-   関数・クラスの目的を明確に記述

## 品質チェック

以下のツールでコード品質をチェックします：

### PHP

-   PHP CS Fixer: コードスタイルチェック・修正
-   PHPUnit: テスト実行

### JavaScript

-   ESLint: コード品質チェック・修正
-   Prettier: コードフォーマット

### 全般

-   EditorConfig: エディタ設定の統一

## 注意事項

-   命名規則に従わないコードはプルリクエストが却下される場合があります
-   新機能追加時は必ずテストを作成してください
-   セキュリティ関連の変更は必ずレビューを受けてください
-   一貫性を保つため、既存のコードスタイルに合わせてください
