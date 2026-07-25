<div class="space-y-4">
  <div class="section-header"><h2>Healthcare Clinic</h2>
    <button class="btn btn-primary" onclick="openPatientForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Register Patient</button></div>
  <div id="hc-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="patient-table"></div>
  <div id="pat-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Register Patient</h3>
      <button onclick="document.getElementById('pat-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Full Name *</label><input id="p-name" class="bs-input" placeholder="Patient name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Age</label><input id="p-age" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Gender</label>
            <select id="p-gender" class="bs-input"><option>Male</option><option>Female</option></select></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Complaint</label><input id="p-complaint" class="bs-input" placeholder="Chief complaint"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Priority</label>
          <select id="p-priority" class="bs-input"><option>Normal</option><option>Urgent</option><option>Emergency</option></select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="savePatient()">Register</button>
          <button class="btn btn-secondary" onclick="document.getElementById('pat-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let patients=[
  {id:'PT-0891',name:'Mama Zaituni',age:45,gender:'Female',complaint:'Malaria symptoms',status:'Waiting',priority:'Urgent',date:today()},
  {id:'PT-0890',name:'Baba Hassan',age:62,complaint:'BP check',gender:'Male',status:'With Doctor',priority:'Normal',date:today()},
  {id:'PT-0889',name:'Mtoto Juma',age:5,complaint:'Fever & cough',gender:'Male',status:'Done',priority:'Normal',date:today()},
  {id:'PT-0888',name:'Bi Fatuma',age:38,complaint:'Antenatal visit',gender:'Female',status:'Done',priority:'Normal',date:today()},
];
const PS={Waiting:'#F59E0B','With Doctor':'#2563EB',Done:'#16A34A',Emergency:'#EF4444'};
const PP={Emergency:'#EF4444',Urgent:'#F59E0B',Normal:'#16A34A'};
function render(){
  const waiting=patients.filter(p=>p.status==='Waiting').length;
  const withDoc=patients.filter(p=>p.status==='With Doctor').length;
  const done=patients.filter(p=>p.status==='Done').length;
  const urgent=patients.filter(p=>p.priority==='Urgent'||p.priority==='Emergency').length;
  document.getElementById('hc-kpis').innerHTML=
    buildKpi('Waiting',String(waiting),'',null,'#F59E0B')+
    buildKpi('With Doctor',String(withDoc),'',null,'#2563EB')+
    buildKpi('Seen Today',String(done),'',null,'#16A34A')+
    buildKpi('Urgent/Emergency',String(urgent),'',null,'#EF4444');
  document.getElementById('patient-table').innerHTML=buildTable(
    ['Patient #','Name','Age','Gender','Complaint','Priority','Status',''],
    patients,p=>`<tr>
      <td class="font-mono text-[11px] text-slate-500">${p.id}</td>
      <td class="font-semibold text-[#111827]">${p.name}</td>
      <td>${p.age}</td>
      <td>${p.gender}</td>
      <td class="text-slate-500">${p.complaint}</td>
      <td>${buildPill(p.priority,PP[p.priority]||'#94A3B8')}</td>
      <td>${buildPill(p.status,PS[p.status]||'#94A3B8')}</td>
      <td><select onchange="updatePatient('${p.id}',this.value)" class="text-[11px] border border-slate-200 rounded-lg px-2 py-1">
        ${['Waiting','With Doctor','Done'].map(s=>`<option ${s===p.status?'selected':''} value="${s}">${s}</option>`).join('')}
      </select></td>
    </tr>`);
  lucide.createIcons();
}
render();
function openPatientForm(){document.getElementById('pat-modal').classList.remove('hidden');}
async function savePatient(){
  const name=document.getElementById('p-name').value.trim();
  if(!name){toast('Name required','error');return;}
  patients.unshift({id:'PT-'+uid(),name,age:Number(document.getElementById('p-age').value)||0,
    gender:document.getElementById('p-gender').value,complaint:document.getElementById('p-complaint').value,
    priority:document.getElementById('p-priority').value,status:'Waiting',date:today()});
  try{await insertRow('patients',{full_name:name,status:'Waiting'});toast('Patient registered!');}catch(e){toast('Added locally');}
  document.getElementById('pat-modal').classList.add('hidden');render();
}
function updatePatient(id,status){patients=patients.map(p=>p.id===id?{...p,status}:p);updateRow('patients',id,{status}).catch(()=>{});render();}
</script>