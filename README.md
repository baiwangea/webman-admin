# Digital Bank Project

[![License](https://img.shields.io/github/license/your-username/digital-bank)](https://github.com/your-username/digital-bank/blob/main/LICENSE)
[![Backend](https://img.shields.io/badge/Backend-Webman%20PHP-blue)](https://www.workerman.net/webman)
[![Frontend](https://img.shields.io/badge/Frontend-Vue%2FReact%20(Placeholder)-green)](https://vuejs.org/ / https://react.dev/)

## 简介 (Introduction)

**Digital Bank** 是一个基于 `Webman` 框架开发的现代化数字银行管理系统。本项目采用前后端分离架构，后端基于高性能的 `Webman` (Workerman) PHP 框架，前端则通常采用主流的 JavaScript 框架（如 Vue.js 或 React.js）。通过模块化的插件设计，系统提供了强大的后台管理功能和全面的银行业务处理能力。

**Digital Bank** is a modern digital banking management system developed based on the `Webman` framework. This project adopts a front-end and back-end separation architecture, with the back-end built on the high-performance `Webman` (Workerman) PHP framework, and the front-end typically using mainstream JavaScript frameworks (such as Vue.js or React.js). Through a modular plugin design, the system provides powerful backend management capabilities and comprehensive banking business processing functionalities.

## 核心特性 (Core Features)

本项目主要由 `saiadmin` 和 `bank` 两个核心插件驱动，提供以下主要功能：

This project is primarily driven by two core plugins, `saiadmin` and `bank`, offering the following main functionalities:

### 1. 后台管理系统 (`saiadmin` Plugin)

`saiadmin` 提供了一个功能强大且全面的后台管理框架，涵盖了系统管理、权限控制、日志审计和开发工具等多个方面。

`saiadmin` provides a powerful and comprehensive backend management framework, covering system administration, permission control, log auditing, and development tools.

*   **系统管理 (System Management)**:
    *   **用户管理 (User Management)**: 完整的用户生命周期管理，包括用户创建、编辑、删除、密码重置等。
    *   **角色管理 (Role Management)**: 基于角色的访问控制 (RBAC)，灵活配置不同角色的权限。
    *   **部门管理 (Department Management)**: 组织架构管理，支持多级部门。
    *   **岗位管理 (Post Management)**: 职位管理。
    *   **菜单管理 (Menu Management)**: 动态菜单配置，控制用户界面和访问权限。
    *   **字典管理 (Dictionary Management)**: 维护系统常用的键值对数据，便于统一管理。
    *   **附件管理 (Attachment Management)**: 文件上传与管理。
    *   **通知公告 (Announcement Management)**: 发布和管理系统公告。
    *   **系统设置 (System Settings)**: 配置系统各项参数，如邮件服务、系统名称等。
*   **日志审计 (Log Auditing)**:
    *   **登录日志 (Login Logs)**: 记录用户登录信息，便于安全审计。
    *   **操作日志 (Operation Logs)**: 记录用户在系统中的关键操作，实现可追溯性。
    *   **邮件日志 (Email Logs)**: 记录系统发送邮件的历史。
*   **系统监控 (System Monitoring)**:
    *   **服务监控 (Service Monitoring)**: 实时查看服务器资源使用情况。
    *   **数据库维护 (Database Maintenance)**: 提供数据库备份、恢复、优化、碎片整理等功能。
*   **开发工具 (Development Tools)**:
    *   **定时任务 (Crontab)**: 可视化管理和配置定时任务。
    *   **代码生成器 (Code Generator)**: 根据数据库表结构自动生成前后端代码，大幅提升开发效率。
    *   **谷歌身份验证器 (Google Authenticator)**: 支持两步验证 (2FA)，增强账户安全性。

### 2. 银行业务核心 (`bank` Plugin)

`bank` 插件封装了数字银行的核心业务逻辑，提供了全面的金融服务管理功能。

The `bank` plugin encapsulates the core business logic of the digital bank, providing comprehensive financial service management capabilities.

*   **用户与账户 (Users & Accounts)**:
    *   **用户管理 (Users Management)**: 银行用户管理。
    *   **账户管理 (Account Management)**: 银行账户的创建、查询、更新等。
    *   **账户记录管理 (Account Record Management)**: 详细的账户交易记录管理。
    *   **用户流水账单管理 (User Transaction Bills Management)**: 生成和管理用户的交易流水账单。
    *   **流水账单记录管理 (Transaction Bills Record Management)**: 记录和查询所有交易流水。
*   **存款与产品 (Deposits & Products)**:
    *   **存款管理 (Deposit Management)**: 存款业务的录入、查询、管理。
    *   **存款等级管理 (Deposit Level Management)**: 配置和管理不同存款产品的等级或阶梯。
    *   **存款库存管理 (Deposit Stock Management)**: 管理存款产品的可用额度或库存。
*   **特色功能 (Special Features)**:
    *   **挖矿管理 (Mine Management)**: 可能涉及虚拟货币挖矿或积分奖励机制。
    *   **挖矿领取管理 (Claim Mine Management)**: 管理挖矿奖励的领取。
    *   **领取管理 (Claim Management)**: 通用的奖励或权益领取管理。
*   **运营管理 (Operations Management)**:
    *   **公告管理 (Announcement Management)**: 发布银行相关的公告。
    *   **订单管理 (Orders Management)**: 统一管理各类业务订单。
    *   **平台亏损管理 (Platform Loss Management)**: 监控和管理平台运营中的亏损情况。
    *   **自定义数据管理 (Custom Data Management)**: 灵活管理自定义业务数据。

## 技术栈 (Technology Stack)

*   **后端 (Backend)**:
    *   **PHP**: 编程语言。
    *   **Webman**: 基于 Workerman 的高性能 PHP 框架。
    *   **数据库**: (假设 MySQL/MariaDB，可根据实际情况修改)
*   **前端 (Frontend)**:
    *   **JavaScript Framework**: Vue.js / React.js (请根据实际项目修改)
    *   **UI Library**: (例如 Element UI / Ant Design Vue / Ant Design React，可根据实际情况修改)

## 项目结构 (Project Structure)

```
.
├── frontend/             # 前端项目代码 (Frontend project code)
├── webman/               # 后端 Webman 项目代码 (Backend Webman project code)
│   ├── app/              # Webman 核心应用目录 (Webman core application directory)
│   ├── config/           # Webman 配置目录 (Webman configuration directory)
│   ├── plugin/           # Webman 插件目录 (Webman plugin directory)
│   │   ├── bank/         # 数字银行核心业务插件 (Digital bank core business plugin)
│   │   └── saiadmin/     # 后台管理系统插件 (Backend management system plugin)
│   └── ...
└── README.md             # 项目根目录 README 文件 (Project root README file)
```

## 安装与运行 (Installation & Running)

### 前提条件 (Prerequisites)

*   PHP >= 8.0 (推荐)
*   Composer
*   Node.js & npm / Yarn
*   数据库 (例如 MySQL/MariaDB)

### 后端安装 (Backend Installation)

1.  进入 `webman` 目录:
    ```bash
    cd webman
    ```
2.  安装 Composer 依赖:
    ```bash
    composer install
    ```
3.  配置 `.env` 文件 (从 `.env.example` 复制并修改数据库连接等配置):
    ```bash
    cp .env.example .env
    # 编辑 .env 文件
    ```
4.  运行数据库迁移和填充 (如果插件提供了):
    ```bash
    # 根据 saiadmin 和 bank 插件的文档执行数据库迁移和填充命令
    # 例如：php webman migrate
    ```
5.  启动 Webman 服务:
    ```bash
    php start.php start # Linux/macOS
    # 或者
    php windows.php     # Windows
    ```

### 前端安装 (Frontend Installation)

1.  进入 `frontend` 目录:
    ```bash
    cd frontend
    ```
2.  安装 npm/Yarn 依赖:
    ```bash
    npm install # 或者 yarn install
    ```
3.  配置 API 请求地址 (通常在 `.env` 或 `src/config.js` 中配置后端地址):
    ```bash
    # 编辑前端配置文件，指向后端服务地址，例如：VITE_APP_API_BASE_URL=http://127.0.0.1:8787
    ```
4.  启动前端开发服务器:
    ```bash
    npm run dev # 或者 yarn dev
    ```

## 访问 (Access)

*   **后台管理系统 (Backend Admin Panel)**: 通常在后端服务启动后，通过前端项目访问。默认地址可能为 `http://localhost:8787/admin` (具体取决于前端路由和后端配置)。
*   **数字银行前端 (Digital Bank Frontend)**: 通常在前端开发服务器启动后访问，例如 `http://localhost:3000` (具体取决于前端配置)。

## 贡献 (Contributing)

欢迎所有形式的贡献！如果您有任何建议、功能请求或 Bug 报告，请随时提交 Issue 或 Pull Request。

Contributions are welcome in all forms! If you have any suggestions, feature requests, or bug reports, please feel free to submit an Issue or Pull Request.

## 许可证 (License)

本项目采用 MIT 许可证。详情请参阅 [LICENSE](LICENSE) 文件。

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
