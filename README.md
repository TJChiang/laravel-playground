# Laravel Playground

開發環境：
- PHP 8.2/8.3
- Laravel 11.x
- Docker compose v2

## GitHub Action

### 本地測試 Action

可以是使用 [act](https://github.com/nektos/act) 指令

- `shivammathur/setup-php` 官方建議 [做法](https://github.com/shivammathur/setup-php?tab=readme-ov-file#local-testing-setup)
```shell
# For runs-on: ubuntu-latest
act -P ubuntu-latest=shivammathur/node:latest

# For runs-on: ubuntu-22.04
act -P ubuntu-22.04=shivammathur/node:2204

# For runs-on: ubuntu-20.04
act -P ubuntu-20.04=shivammathur/node:2004
```

- 觸發 git push 情境
```shell
act push --container-architecture linux/amd64
```

- 如果是 mac M 系列晶片的話，可以加上 `--container-architecture linux/amd64`
```shell
act push --container-architecture linux/amd64
```

- Action Cache host 問題：[在 Local 測試 GitHub Action 搭配 Cache 機制](https://engineering.linecorp.com/zh-hant/blog/github-actions-with-act)

