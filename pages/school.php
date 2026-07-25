<div class="space-y-4">
  <div class="section-header"><h2>School Management</h2>
    <button class="btn btn-primary" onclick="openStudentForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Enroll Student</button></div>
  <div id="sch-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="students-table"></div>
  <div id="stu-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Enroll Student</h3>
      <button onclick="document.getElementById('stu-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Student Name *</label><input id="st-name" class="bs-input" placeholder="Full name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Class</label>
            <select id="st-class" class="bs-input"><option>Form 1</option><option>Form 2</option><option>Form 3</option><option>Form 4</option><option>Form 5</option><option>Form 6</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Gender</label>
            <select id="st-gender" class="bs-input"><option>Male</option><option>Female</option></select></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Parent/Guardian Phone</label><input id="st-phone" class="bs-input" placeholder="+255 xxx xxx xxx"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Fees (<?= CURRENCY ?>/term)</label><input id="st-fees" type="number" class="bs-input" placeholder="0"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveStudent()">Enroll</button>
          <button class="btn btn-secondary" onclick="document.getElementById('stu-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let students=[
  {id:'STU-1841',name:'Amina Hassan',class:'Form 4',gender:'Female',fees:250000,paid:250000,balance:0,status:'Active'},
  {id:'STU-1840',name:'Juma Kilimo',class:'Form 2',gender:'Male',fees:250000,paid:150000,balance:100000,status:'Active'},
  {id:'STU-1839',name:'Zaituni Mohamed',class:'Form 6',gender:'Female',fees:320000,paid:320000,balance:0,status:'Active'},
  {id:'STU-1838',name:'Hassan Ally',class:'Form 1',gender:'Male',fees:250000,paid:0,balance:250000,status:'Active'},
  {id:'STU-1837',name:'Fatuma Said',class:'Form 3',gender:'Female',fees:250000,paid:125000,balance:125000,status:'Active'},
];
function render(){
  const total=students.length;
  const males=students.filter(s=>s.gender==='Male').length;
  const fees=students.reduce((s,st)=>s+(st.paid||0),0);
  const unpaid=students.reduce((s,st)=>s+(st.balance||0),0);
  document.getElementById('sch-kpis').innerHTML=
    buildKpi('Total Students',String(total),'+3',true,'#2563EB')+
    buildKpi('Male/Female',males+'/ '+(total-males),'',null,'#7C3AED')+
    buildKpi('Fees Collected',money(fees),'',null,'#16A34A')+
    buildKpi('Outstanding Fees',money(unpaid),'',null,'#EF4444');
  document.getElementById('students-table').innerHTML=buildTable(
    ['ID','Student','Class','Gender','Fees/Term','Paid','Balance','Status'],
    students,s=>`<tr>
      <td class="font-mono text-[11px] text-slate-500">${s.id}</td>
      <td class="font-semibold text-[#111827]">${s.name}</td>
      <td><span class="pill bg-blue-50 text-blue-700">${s.class}</span></td>
      <td>${s.gender}</td>
      <td>${money(s.fees)}</td>
      <td class="text-green-600 font-bold">${money(s.paid)}</td>
      <td class="${s.balance>0?'text-red-600 font-bold':'text-slate-400'}">${s.balance>0?money(s.balance):'Cleared'}</td>
      <td>${buildPill(s.status,'#16A34A')}</td>
    </tr>`);
  lucide.createIcons();
}
render();
function openStudentForm(){document.getElementById('stu-modal').classList.remove('hidden');}
async function saveStudent(){
  const name=document.getElementById('st-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const fees=Number(document.getElementById('st-fees').value)||0;
  students.unshift({id:'STU-'+uid(),name,class:document.getElementById('st-class').value,
    gender:document.getElementById('st-gender').value,fees,paid:0,balance:fees,status:'Active'});
  try{await insertRow('school_students',{full_name:name,status:'Active'});toast('Student enrolled!');}catch(e){toast('Added locally');}
  document.getElementById('stu-modal').classList.add('hidden');render();
}
</script>