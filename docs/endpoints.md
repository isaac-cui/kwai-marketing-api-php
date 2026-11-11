# 接口索引

依据[主手册](https://docs.qingque.cn/d/home/eZQD_B7D-wuESSFNbPJJqrt2c?identityId=1ysd5x6cFON)及其引用的[商务管理手册](https://docs.qingque.cn/d/home/eZQBnkVLWSKMdMPkMqTvvw3vc?identityId=1oEElSWqQi3)，核对日期：2026-11-03。61 个业务接口；OAuth 和上传协议另见 README。以下业务方法均发送 POST JSON。

| 分类 | Go 方法 / PHP 方法 | 路径（统一前缀 `/rest/n/mapi/`） | 用途 |
|---|---|---|---|
| Reports | `AccountEffect` / `accountEffect` | `report/dspAccountEffectQuery` | 广告主消耗 |
| Reports | `AgentEffect` / `agentEffect` | `report/crmAccountQuery` | 代理商消耗 |
| Reports | `AgentBalance` / `agentBalance` | `report/financeBalanceQuery` | 代理商余额 |
| Reports | `AgentMonthlyConsumption` / `agentMonthlyConsumption` | `report/agentMonthlyConsumptionReportQuery` | 代理商月度消耗 |
| Reports | `AccountMonthlyConsumption` / `accountMonthlyConsumption` | `report/accountMonthlyConsumptionReportQuery` | 广告主月度消耗 |
| Reports | `AccountMonthlyDetailConsumption` / `accountMonthlyDetailConsumption` | `report/accountMonthlyDetailConsumptionReportQuery` | 广告主消耗明细 |
| Reports | `CampaignEffect` / `campaignEffect` | `report/dspCampaignEffectQuery` | 广告计划报表 |
| Reports | `UnitEffect` / `unitEffect` | `report/dspUnitEffectQuery` | 广告组报表 |
| Reports | `CreativeEffect` / `creativeEffect` | `report/dspCreativeEffectQuery` | 广告创意报表 |
| Reports | `VideoEffect` / `videoEffect` | `report/dspPhotoEffectQuery` | 视频报表 |
| Reports | `AppTargetEffect` / `appTargetEffect` | `report/dspAppTargetEffectQuery` | App 分国家报表 |
| Reports | `PopulationEffect` / `populationEffect` | `report/dspPopulationAnalysisEffectQuery` | 投放地/操作系统报表 |
| Reports | `AccountInfos` / `accountInfos` | `report/dspAccountInfosQuery` | 账户详情 |
| Reports | `Accounts` / `accounts` | `report/crmAccountQueryByAgentOrCorp` | 账户列表 |
| Reports | `AdxDaily` / `adxDaily` | `adx/getAdxReport/day` | 三方广告日报 |
| Finance | `AccountBalance` / `accountBalance` | `finance/financeAccountBalanceQuery` | 广告主余额 |
| Campaigns | `List` / `list` | `campaign/dspCampaignPageQueryPerformance` | Campaign List |
| Campaigns | `Create` / `create` | `campaign/dspCampaignAddPerformance` | Campaign Create |
| Campaigns | `Update` / `update` | `campaign/dspCampaignUpdatePerformance` | Campaign Update |
| Campaigns | `UpdateStatus` / `updateStatus` | `campaign/dspCampaignUpdateOpenStatusPerformance` | Campaign UpdateStatus |
| Campaigns | `Delete` / `delete` | `campaign/dspCampaignDeletePerformance` | Campaign Delete |
| Units | `List` / `list` | `unit/dspUnitPageQueryPerformance` | Unit List |
| Units | `Create` / `create` | `unit/dspUnitAddPerformance` | Unit Create |
| Units | `Update` / `update` | `unit/dspUnitUpdatePerformance` | Unit Update |
| Units | `UpdateStatus` / `updateStatus` | `unit/dspUnitUpdateOpenStatusPerformance` | Unit UpdateStatus |
| Units | `Delete` / `delete` | `unit/dspUnitDeletePerformance` | Unit Delete |
| Creatives | `List` / `list` | `creative/dspCreativePageQueryPerformance` | Creative List |
| Creatives | `Create` / `create` | `creative/dspCreativeAddPerformance` | Creative Create |
| Creatives | `Update` / `update` | `creative/dspCreativeUpdatePerformance` | Creative Update |
| Creatives | `UpdateStatus` / `updateStatus` | `creative/dspCreativeUpdateOpenStatusPerformance` | Creative UpdateStatus |
| Creatives | `Delete` / `delete` | `creative/dspCreativeDeletePerformance` | Creative Delete |
| Units | `AuditResult` / `auditResult` | `unit/dspUnitAuditResultPerformance` | 广告组审核建议 |
| Units | `UpdateBudget` / `updateBudget` | `unit/dspUnitUpdateBudgetPerformance` | 批量修改预算 |
| Units | `UpdateBid` / `updateBid` | `unit/dspUnitUpdateBidPerformance` | 批量修改出价 |
| Units | `UpdateCboBid` / `updateCboBid` | `unit/dspUnitUpdateCboBidPerformance` | CBO 出价 |
| Units | `DerivedActionTypes` / `derivedActionTypes` | `unit/derivedActionType` | 衍生行为 |
| Creatives | `AuditResult` / `auditResult` | `creative/dspCreativeAuditResultPerformance` | 创意审核建议 |
| Creatives | `PostAuditResult` / `postAuditResult` | `post/profilePostAuditResultPerformance` | 社区审核建议 |
| Materials | `Apps` / `apps` | `material/dspMaterialAppQuery` | 应用查询（接口清单路径，详见文档差异说明） |
| Materials | `Pixels` / `pixels` | `material/dspMaterialPixelQuery` | Pixel 查询 |
| Materials | `Videos` / `videos` | `material/dspMaterialVideoQuery` | 视频查询 |
| Materials | `UploadToken` / `uploadToken` | `material/dspMaterialFileUploadToken` | 获取单文件上传令牌 |
| Materials | `TransferVideo` / `transferVideo` | `material/dspMaterialVideoTransfer` | 发起/查询视频转码 |
| Materials | `CreatePwa` / `createPwa` | `material/createPWA` | 创建 PWA |
| Materials | `Pwas` / `pwas` | `material/pageQueryPWA` | 查询 PWA |
| Materials | `RefreshPwa` / `refreshPwa` | `material/refreshPWA` | 重试 PWA |
| Materials | `AppealPwa` / `appealPwa` | `material/appealPWA` | 申诉 PWA |
| Materials | `Avatars` / `avatars` | `material/dspMaterialAvatarQuery` | 头像 |
| Materials | `Populations` / `populations` | `material/dspMaterialPopulationQuery` | 人群包 |
| Materials | `Playables` / `playables` | `material/dspMaterialPlayableQuery` | 试玩素材 |
| Materials | `Interactives` / `interactives` | `material/dspMaterialInteractiveQuery` | 互动样式 |
| Organic | `Accounts` / `accounts` | `organic/authKwaiAccountQuery` | 已授权原生账户 |
| Organic | `Posts` / `posts` | `organic/profilePostQuery` | 原生作品（已弃用） |
| Organic | `PostsV2` / `postsV2` | `organic/profilePostQueryV2` | 原生作品 V2 |
| Organic | `PushVideo` / `pushVideo` | `organic/profileMaterialVideoPush` | 视频同步至原生账户 |
| Business | `LicenseUploadToken` / `licenseUploadToken` | `bc/bcLicenseFileUploadToken` | 资质文件上传令牌 |
| Business | `LicenseFileKey` / `licenseFileKey` | `bc/bcLicenseFileUploadTokenToFileKey` | 资质文件唯一标识 |
| Business | `CreateAccount` / `createAccount` | `bc/bcAdAccountCreate` | 新建广告主账户 |
| Business | `Account` / `account` | `bc/bcAccountQueryByAdAccount` | 查询广告主账户 |
| Finance | `Charge` / `charge` | `finance/chargeFinance` | 广告主账户充值 |
| Finance | `Refund` / `refund` | `finance/refundMoney` | 广告主账户退款 |

文档差异与约定：

- `Materials.Apps` 使用接口清单中的 `dspMaterialAppQuery`，详细章节列为 `/material/c`，需真实账号联调确认；可使用通用 Call/call 覆盖路径。
- 应用新建在文档中明确暂停，不提供入口。原生作品 `Posts` 已弃用，优先使用 `PostsV2`。
- PWA、部分原生和资质 Token 章节未单独标注 HTTP 方法，SDK 按手册统一请求规范使用 POST，需真实环境确认。
- 资质文件 `/api/upload` 的表格说 `result=1`，示例却是 `status=1`；License/license 兼容两者，视频分片仍严格检查 `result=1`。
- `Business.Account` 的标题为分页查询，但请求表仅列 `accountId`；调用时保留该原始契约，不推测其他必填字段。
- 投放地和操作系统报表共用 PopulationEffect，传对应筛选维度。
- 请求字段沿用文档原名。未做全量字段建模、本地枚举或业务关联校验；权限、预算与出价约束由服务端校验。响应保留完整业务数据。
- 金额不自动转换：广告预算/出价通常以 10^6 放大值传入；充值/退款使用 `chargeMoney`/`refundMoney` 对象中的整数 `units` 与 `micros`，例如 10.5 为 `{ "units": 10, "micros": 500000 }`。各接口以其字段表为准。

