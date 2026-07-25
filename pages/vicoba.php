<div class="space-y-4">
  <div class="section-header"><h2>VICOBA &amp; SACCOS</h2>
    <button class="btn btn-primary" onclick="openGroupForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Group</button></div>
  <div id="vic-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="groups-table"></div>
  <div id="grp-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Register VICOBA Group</h3>
      <button onclick="document.getElementById('grp-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Group Name *</label><input id="g-name" class="bs-input" placeholder="e.g. Mama Lishe VICOBA"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Members</label><input id="g-members" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Weekly Contribution (<?= CURRENCY ?>)</label><input id="g-contrib" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Meeting Day</label>
          <select id="g-day" class="bs-input"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveGroup()">Register Group</button>
          <button class="btn btn-secondary" onclick="document.getElementById('grp-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let groups=[
  {id:'VIC-041',name:'Mama Lishe VICOBA',members:25,contribution:5000,savings:3250000,loans_out:1200000,cycle:3,day:'Wednesday',status:'Active'},
  {id:'VIC-040',name:'Kilimani Women Group',members:20,contribution:10000,savings:5800000,loans_out:2100000,cycle:5,day:'Friday',status:'Active'},
  {id:'VIC-039',name:'Vijana Entrepreneurs',members:15,contribution:20000,savings:4200000,loans_out:1800000,cycle:2,day:'Saturday',status:'Active'},
  {id:'VIC-038',name:'Dodoma Farmers SACCO',members:45,contribution:15000,savings:9800000,loans_out:4500000,cycle:7,day:'Monday',status:'Active'},
];
function render(){
  const totalMembers=groups.reduce((s,g)=>s+(g.members||0),0);
  const totalSavings=groups.reduce((s,g)=>s+(g.savings||0),0);
  const totalLoans=groups.reduce((s,g)=>s+(g.loans_out||0),0);
  document.getElementById('vic-kpis').innerHTML=
    buildKpi('Total Groups',String(groups.length),'',null,'#2563EB')+
    buildKpi('Total Members',String(totalMembers),'+5',true,'#16A34A')+
    buildKpi('Total Savings',money(totalSavings),'',null,'#7C3AED')+
    buildKpi('Loans Outstanding',money(totalLoans),'',null,'#EF4444');
  document.getElementById('groups-table').innerHTML=buildTable(
    ['Group ID','Name','Members','Weekly Contrib','Total Savings','Loans Out','Cycle','Meeting Day','Status'],
    groups,g=>`<tr>
      <td class="font-mono text-[11px] text-slate-500">${g.id}</td>
      <td class="font-semibold text-[#111827]">${g.name}</td>
      <td class="font-bold">${g.members}</td>
      <td>${money(g.contribution)}/week</td>
      <td class="font-bold text-[#16A34A]">${money(g.savings)}</td>
      <td class="text-red-600 font-bold">${money(g.loans_out)}</td>
      <td>Cycle ${g.cycle}</td>
      <td><span class="pill bg-blue-50 text-blue-700">${g.day}</span></td>
      <td>${buildPill(g.status||'Active','#16A34A')}</td>
    </tr>`);
  lucide.createIcons();
}
render();
function openGroupForm(){document.getElementById('grp-modal').classList.remove('hidden');}
async function saveGroup(){
  const name=document.getElementById('g-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const members=Number(document.getElementById('g-members').value)||0;
  const contribution=Number(document.getElementById('g-contrib').value)||0;
  groups.unshift({id:'VIC-'+uid(),name,members,contribution,savings:0,loans_out:0,cycle:1,day:document.getElementById('g-day').value,status:'Active'});
  try{await insertRow('vicoba_groups',{group_name:name,member_count:members,status:'Active'});toast('Group registered!');}catch(e){toast('Added locally');}
  document.getElementById('grp-modal').classList.add('hidden');render();
}
</script>