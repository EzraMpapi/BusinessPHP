<?php
// Use ROOT constant set by the entry point, or default to parent dir
if (!defined('ROOT')) define('ROOT', dirname(__DIR__));

$page    = isset($_GET['page']) ? preg_replace('/[^a-z0-9_]/', '', $_GET['page']) : 'dashboard';
$modules = json_decode(MODULES, true);
$groups  = ['Core','Operations','Intelligence','Industry','Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title><?= htmlspecialchars(APP_NAME) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/dist/umd/supabase.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="bg-slate-50" style="font-family:'Inter',system-ui,sans-serif">

<script>
window.BS = {
  url:      '<?= SUPABASE_URL ?>',
  key:      '<?= SUPABASE_ANON_KEY ?>',
  currency: '<?= CURRENCY ?>',
  company:  '<?= COMPANY_NAME ?>'
};
</script>

<div id="app" class="flex h-screen overflow-hidden">

  <!-- SIDEBAR -->
  <aside id="sidebar" class="w-[210px] bg-[#111827] text-white flex flex-col flex-shrink-0 transition-all duration-200 overflow-hidden">
    <div class="flex items-center gap-2.5 px-3.5 h-[54px] border-b border-white/10 flex-shrink-0">
      <div class="w-7 h-7 rounded-lg bg-[#16A34A] flex items-center justify-center font-black text-[13px] flex-shrink-0">B</div>
      <span class="sidebar-label font-black text-[14px]">Business<span class="text-[#16A34A]">Sphere</span></span>
    </div>
    <div class="px-2 py-2 border-b border-white/10 flex-shrink-0">
      <div class="flex items-center gap-2 bg-white/10 rounded-lg px-2.5 py-1.5">
        <i data-lucide="search" class="w-3 h-3 text-slate-400 flex-shrink-0"></i>
        <input id="mod-search" type="text" placeholder="Search..."
          class="bg-transparent text-[11px] text-white placeholder-slate-400 outline-none w-full sidebar-label"/>
      </div>
    </div>
    <nav class="flex-1 overflow-y-auto py-2">
      <?php foreach ($groups as $grp): ?>
      <div class="grp mb-1">
        <p class="px-3.5 py-1 text-[9px] font-bold text-slate-500 uppercase tracking-widest sidebar-label"><?= $grp ?></p>
        <?php foreach ($modules as $m): if ($m['group'] !== $grp) continue; ?>
        <a href="?page=<?= $m['id'] ?>"
           class="nav-link flex items-center gap-2 px-2.5 py-1.5 mx-1 rounded-lg transition-all <?= $page===$m['id']?'bg-[#16A34A] text-white':'text-slate-400 hover:text-white hover:bg-white/5' ?>"
           style="width:calc(100% - 8px)">
          <i data-lucide="<?= $m['icon'] ?>" class="w-3.5 h-3.5 flex-shrink-0"></i>
          <span class="text-[11.5px] font-medium truncate sidebar-label"><?= htmlspecialchars($m['label']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </nav>
    <div class="border-t border-white/10 p-2 flex-shrink-0">
      <button onclick="toggleSidebar()" class="w-full flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-slate-400 hover:text-white transition-all">
        <i data-lucide="panel-left-close" class="w-3.5 h-3.5 flex-shrink-0"></i>
        <span class="text-[11px] sidebar-label">Collapse</span>
      </button>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="flex-1 flex flex-col overflow-hidden min-w-0">
    <header class="h-[54px] bg-white border-b border-slate-200 flex items-center gap-3 px-4 flex-shrink-0 shadow-sm">
      <button onclick="toggleSidebar()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100">
        <i data-lucide="menu" class="w-4 h-4"></i>
      </button>
      <?php $cur=null; foreach($modules as $m){if($m['id']===$page){$cur=$m;break;}} ?>
      <?php if($cur): ?>
      <div class="flex items-center gap-2">
        <i data-lucide="<?= $cur['icon'] ?>" class="w-4 h-4 text-[#16A34A]"></i>
        <h1 class="text-[15px] font-bold text-[#111827]"><?= htmlspecialchars($cur['label']) ?></h1>
      </div>
      <?php endif; ?>
      <div class="ml-auto flex items-center gap-3">
        <button class="relative w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100">
          <i data-lucide="bell" class="w-4 h-4"></i>
          <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
        <div class="hidden sm:block text-right">
          <p class="text-[12px] font-semibold text-[#111827]"><?= htmlspecialchars(COMPANY_NAME) ?></p>
          <p class="text-[10px] text-slate-400">Admin</p>
        </div>
        <div class="w-8 h-8 rounded-xl bg-[#16A34A] flex items-center justify-center text-white font-bold text-[12px]">A</div>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4">
      <?php
        // Look for page file in the pages directory
        $page_file = ROOT . '/pages/' . $page . '.php';
        if (file_exists($page_file)) {
            include $page_file;
        } else {
            include ROOT . '/pages/coming-soon.php';
        }
      ?>
    </main>
  </div>
</div>

<div id="toasts" class="fixed bottom-5 right-5 z-50 space-y-2 pointer-events-none"></div>
<script src="/assets/app.js"></script>
<script>
  lucide.createIcons();
  document.getElementById('mod-search').addEventListener('input',function(){
    const q=this.value.toLowerCase();
    document.querySelectorAll('.nav-link').forEach(el=>{
      const t=el.querySelector('span')?.textContent.toLowerCase()||'';
      el.style.display=t.includes(q)?'':'none';
    });
    document.querySelectorAll('.grp').forEach(g=>{
      const v=[...g.querySelectorAll('.nav-link')].some(e=>e.style.display!=='none');
      g.style.display=v?'':'none';
    });
  });
</script>
</body>
</html>
