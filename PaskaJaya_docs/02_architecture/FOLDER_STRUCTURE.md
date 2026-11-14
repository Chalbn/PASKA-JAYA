# Struktur Folder
```
app/
  Enums/ Role, PaymentMethod, PaymentStatus, DeliveryStatus, UserStatus
  Http/Controllers/
    Auth/ (Login, Register, Otp, Logout)
    Admin/ (Dashboard, User, Team)
    Owner/ (Dashboard, SalesReport, Bon[read], Attendance[read])
    Kasir/ (Home, Sale, Balance, WeeklyChart, DrawerHistory, BonCreate, Bon, DailyMaterial, Attendance)
    Karyawan/ (Delivery)
    Shared/ (Item, Profile)
  Http/Middleware/ EnsureRole, EnsureActive
  Http/Requests/...
  Models/ User, Category, Item, StockMovement, Sale, SaleItem, Bon, BonItem, BonPayment, BonStatusLog,
          DrawerEntry, DrawerClosing, DailyMaterial, Attendance, TeamMember, Setting
  Policies/
  Services/ StockService, SaleService, BonService, DrawerService, ReportService, SystemStatusService
  Console/Commands/ ResetDrawer, RunBackup
database/ migrations, factories, seeders
resources/js/
  app.tsx
  Layouts/ AuthLayout, AppLayout (sidebar+topbar per role)
  Components/ui/ Button, Card, Badge, Modal, ConfirmDialog, Input, Select, Table, Tabs, Stat, Timeline, EmptyState, ImageUpload
  Components/domain/ ItemCard, ItemTable, ItemFormModal, BonDetailModal, MethodBadge, PaymentBadge, DeliveryBadge
  Pages/
    Auth/ Login, Register, VerifyOtp
    Admin/ Dashboard, Users, Team
    Owner/ Dashboard, SalesReport, Bons, Attendance
    Kasir/ Home, Sale, Balance, Weekly, DrawerHistory, BonCreate, Bons, DailyMaterials, Attendance
    Karyawan/ Deliveries, DeliveryShow
    Shared/ Items, Profile
  lib/ format.ts, menu.ts, utils.ts
  types/
routes/ web.php (group per role)
```
