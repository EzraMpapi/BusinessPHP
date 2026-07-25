<div class="space-y-4">
  <div class="section-header"><h2>Projects</h2>
    <button class="btn btn-primary" onclick="openProjForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Project</button>
  </div>
  <div id="proj-kpis" class="grid-4" style="gap:12px"></div>
  <div id="proj-list" class="space-y-3"></div>
  <div id="proj-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px] text-[#111827]">New Project</h3>
        <button onclick="document.getElementById('proj-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Project Name *</label><input id="pj-name" class="bs-input" placeholder="Project name"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Client</label><input id="pj-client" class="bs-input" placeholder="Client name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Budget (<?= CURRENCY ?>)</label><input id="pj-budget" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Deadline</label><input id="pj-due" type="date" class="bs-input"/></div>
        </div>
        <div class="flex gap-2 pt-2"><button class="btn btn-primary flex-1" onclick="saveProject()">Create Project</button>
          <button class="btn btn-secondary" onclick="document.getElementById('proj-modal').classList.add('hidden')">Cancel</button></div>
      </div>
    </div>
  </div>
</div>
<script>
const PROJ_COLOR={Active:'#16A34A',Planning:'#2563EB',OnHold:'#F59E0B',Completed:'#7C3AED'};
let projects=[
  {id:1,name:'ERP Integration',client:'Kilosa Hardware',budget:8500000,spent:4200000,progress:52,status:'Active',due:'2026-09-01',tasks:12,done:6},
  {id:2,name:'Warehouse Expansion',client:'Internal',budget:15000000,spent:9800000,progress:68,status:'Active',due:'2026-08-15',tasks:20,done:14},
  {id:3,name:'Website Redesign',client:'Moshi Foods',budget:3200000,spent:3200000,progress:100,status:'Completed',due:'2026-07-01',tasks:8,done:8},
  {id:4,name:'Solar Installation',client:'Internal',budget:12000000,spent:0,progress:0,status:'Planning',due:'2026-10-01',tasks:15,done:0},
];
function renderProjects(){
  const active=projects.filter(p=>p.status==='Active').length;
  const totalBudget=projects.reduce((s,p)=>s+p.budget,0);
  const totalSpent=projects.reduce((s,p)=>s+p.spent,0);
  document.getElementById('proj-kpis').innerHTML=
    buildKpi('Total Projects',String(projects.length),'',null,'#2563EB')+
    buildKpi('Active',String(active),'',null,'#16A34A')+
    buildKpi('Total Budget',money(totalBudget),'',null,'#7C3AED')+
    buildKpi('Spent to Date',money(totalSpent),'',null,'#F59E0B');
  document.getElementById('proj-list').innerHTML=projects.map(p=>`
    <div class="card">
      <div class="flex items-center justify-between mb-3">
        <div><h3 class="text-[14px] font-bold text-[#111827]">${p.name}</h3>
          <p class="text-[12px] text-slate-500">${p.client} &middot; Due ${p.due}</p></div>
        <div class="flex items-center gap-2">${buildPill(p.status,PROJ_COLOR[p.status]||'#94A3B8')}
          <span class="text-[11px] text-slate-400">${p.done}/${p.tasks} tasks</span></div>
      </div>
      <div class="flex items-center gap-3 mb-3">
        <div style="flex:1;height:8px;background:#F1F5F9;border-radius:4px">
          <div style="width:${p.progress}%;height:100%;background:${PROJ_COLOR[p.status]||'#94A3B8'};border-radius:4px"></div></div>
        <span class="text-[12px] font-bold text-[#111827]">${p.progress}%</span>
      </div>
      <div class="flex justify-between text-[12px]">
        <span class="text-slate-500">Budget: <strong>${money(p.budget)}</strong></span>
        <span class="text-slate-500">Spent: <strong>${money(p.spent)}</strong></span>
        <span class="${p.spent>p.budget?'text-red-500':'text-green-600'} font-semibold">Remaining: ${money(p.budget-p.spent)}</span>
      </div>
    </div>`).join('');
}
function openProjForm(){document.getElementById('proj-modal').classList.remove('hidden');}
function saveProject(){
  const name=document.getElementById('pj-name').value.trim();
  if(!name){toast('Project name required','error');return;}
  projects.unshift({id:uid(),name,client:document.getElementById('pj-client').value||'Internal',
    budget:Number(document.getElementById('pj-budget').value)||0,spent:0,progress:0,
    status:'Planning',due:document.getElementById('pj-due').value||'TBD',tasks:0,done:0});
  document.getElementById('proj-modal').classList.add('hidden');
  renderProjects();toast('Project created');
}
renderProjects();
</script>
