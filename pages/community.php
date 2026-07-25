<div class="space-y-4">
  <div class="section-header"><h2>Community Groups</h2>
    <button class="btn btn-primary" onclick="openCommForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Register Group</button></div>
  <div id="com-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="comm-table"></div>
  <div id="comm-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Register Community Group</h3>
      <button onclick="document.getElementById('comm-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Group Name *</label><input id="cm-name" class="bs-input" placeholder="Group name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Type</label>
            <select id="cm-type" class="bs-input"><option>Youth</option><option>Women</option><option>Religious</option><option>Environmental</option><option>Sports</option><option>Business</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Members</label><input id="cm-members" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Location/Ward</label><input id="cm-location" class="bs-input" placeholder="Ward/Village name"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveComm()">Register</button>
          <button class="btn btn-secondary" onclick="document.getElementById('comm-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let comm_groups=[
  {id:'CG-121',name:'Vijana wa Maskani',type:'Youth',members:45,location:'Sinza Ward',projects:3,funds:850000,status:'Active',reg_date:'2024-03-15'},
  {id:'CG-120',name:'Mama Nguvu Group',type:'Women',members:30,location:'Kinondoni Ward',projects:2,funds:420000,status:'Active',reg_date:'2023-08-01'},
  {id:'CG-119',name:'Green Kilimanjaro',type:'Environmental',members:22,location:'Moshi',projects:5,funds:1200000,status:'Active',reg_date:'2022-06-10'},
  {id:'CG-118',name:'Simba Sports Club',type:'Sports',members:60,location:'Temeke',projects:1,funds:200000,status:'Active',reg_date:'2021-01-20'},
];
const CT={Youth:'#2563EB',Women:'#EC4899',Religious:'#7C3AED',Environmental:'#16A34A',Sports:'#F59E0B',Business:'#0891B2'};
function render(){
  const total=comm_groups.length;
  const members=comm_groups.reduce((s,g)=>s+(g.members||0),0);
  const projects=comm_groups.reduce((s,g)=>s+(g.projects||0),0);
  const funds=comm_groups.reduce((s,g)=>s+(g.funds||0),0);
  document.getElementById('com-kpis').innerHTML=
    buildKpi('Registered Groups',String(total),'+2',true,'#2563EB')+
    buildKpi('Total Members',String(members),'+15',true,'#16A34A')+
    buildKpi('Active Projects',String(projects),'',null,'#7C3AED')+
    buildKpi('Community Funds',money(funds),'',null,'#F59E0B');
  document.getElementById('comm-table').innerHTML=buildTable(
    ['ID','Group Name','Type','Members','Location','Projects','Funds','Status','Registered'],
    comm_groups,g=>`<tr>
      <td class="font-mono text-[11px] text-slate-500">${g.id}</td>
      <td class="font-semibold text-[#111827]">${g.name}</td>
      <td>${buildPill(g.type,CT[g.type]||'#94A3B8')}</td>
      <td class="font-bold">${g.members}</td>
      <td class="text-slate-500">${g.location}</td>
      <td>${g.projects}</td>
      <td class="font-bold text-[#16A34A]">${money(g.funds)}</td>
      <td>${buildPill(g.status||'Active','#16A34A')}</td>
      <td class="text-slate-500 text-[11px]">${g.reg_date}</td>
    </tr>`);
  lucide.createIcons();
}
render();
function openCommForm(){document.getElementById('comm-modal').classList.remove('hidden');}
async function saveComm(){
  const name=document.getElementById('cm-name').value.trim();
  if(!name){toast('Name required','error');return;}
  comm_groups.unshift({id:'CG-'+uid(),name,type:document.getElementById('cm-type').value,
    members:Number(document.getElementById('cm-members').value)||0,
    location:document.getElementById('cm-location').value,projects:0,funds:0,status:'Active',reg_date:today()});
  try{await insertRow('community_groups',{group_name:name,status:'Active'});toast('Group registered!');}catch(e){toast('Added locally');}
  document.getElementById('comm-modal').classList.add('hidden');render();
}
</script>