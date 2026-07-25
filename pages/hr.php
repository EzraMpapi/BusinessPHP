<div class="space-y-4">
  <div class="section-header">
    <h2>Human Resources</h2>
    <button class="btn btn-primary" onclick="openEmpForm()">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Employee
    </button>
  </div>

  <div id="hr-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
  </div>

  <div id="emp-table"></div>

  <!-- Add Employee Modal -->
  <div id="emp-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">Add Employee</h3>
        <button onclick="document.getElementById('emp-modal').classList.add('hidden')" class="text-slate-400">
          <i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Full Name *</label>
          <input id="e-name" class="bs-input" placeholder="Full name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Role</label>
            <input id="e-role" class="bs-input" placeholder="Job title"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Department</label>
            <select id="e-dept" class="bs-input">
              <option>Sales</option><option>Finance</option><option>HR</option>
              <option>Operations</option><option>Technology</option><option>Marketing</option>
            </select></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Monthly Salary (<?= CURRENCY ?>)</label>
          <input id="e-salary" type="number" class="bs-input" placeholder="e.g. 500000"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveEmployee()">Add Employee</button>
          <button class="btn btn-secondary" onclick="document.getElementById('emp-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let employees = [];

(async function() {
  const rows = await fetchRows('hr_employees');
  employees = rows.length ? rows : window.SEED.employees;
  renderHR();
})();

function renderHR() {
  const active  = employees.filter(e=>e.status==='Active').length;
  const onLeave = employees.filter(e=>e.status==='On Leave').length;
  const payroll = employees.reduce((s,e)=>s+(e.salary||e.monthly_salary||0),0);

  document.getElementById('hr-kpis').innerHTML =
    buildKpi('Total Staff', String(employees.length), '+1', true, '#EC4899') +
    buildKpi('Monthly Payroll', money(payroll), '', null, '#7C3AED') +
    buildKpi('On Leave', String(onLeave), '', null, '#F59E0B');

  document.getElementById('emp-table').innerHTML = buildTable(
    ['Employee','Role','Department','Salary','Status','Joined',''],
    employees,
    emp => `<tr>
      <td>
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-[#16A34A] flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">
            ${(emp.name||emp.full_name||'?').split(' ').map(n=>n[0]).join('').slice(0,2)}
          </div>
          <span class="font-semibold text-[#111827]">${emp.name||emp.full_name||''}</span>
        </div>
      </td>
      <td class="text-slate-500">${emp.role||emp.job_title||''}</td>
      <td><span class="pill" style="background:#EFF6FF;color:#2563EB">${emp.dept||emp.department||''}</span></td>
      <td class="font-bold">${money(emp.salary||emp.monthly_salary)}</td>
      <td>${buildPill(emp.status||'Active', window.STATUS_COLOR[emp.status||'Active']||'#16A34A')}</td>
      <td class="text-slate-500">${emp.joined||emp.join_date||''}</td>
      <td><button onclick="toggleLeave(${emp.id})" class="text-[11px] text-blue-600 font-semibold hover:underline">
        ${emp.status==='On Leave' ? 'Return' : 'Leave'}
      </button></td>
    </tr>`
  );
  lucide.createIcons();
}

function openEmpForm() { document.getElementById('emp-modal').classList.remove('hidden'); }

async function saveEmployee() {
  const name = document.getElementById('e-name').value.trim();
  if (!name) { toast('Name is required','error'); return; }
  const row = {
    full_name: name,
    job_title: document.getElementById('e-role').value,
    department: document.getElementById('e-dept').value,
    monthly_salary: Number(document.getElementById('e-salary').value)||0,
    status: 'Active', join_date: today()
  };
  employees.unshift({id:uid(), name:row.full_name, role:row.job_title,
    dept:row.department, salary:row.monthly_salary, status:'Active', joined:today()});
  try { await insertRow('hr_employees', row); toast('Employee added!'); }
  catch(e) { toast('Added locally'); }
  document.getElementById('emp-modal').classList.add('hidden');
  renderHR();
}

function toggleLeave(id) {
  employees = employees.map(e => e.id==id ? {...e, status:e.status==='On Leave'?'Active':'On Leave'} : e);
  updateRow('hr_employees', id, {status: employees.find(e=>e.id==id)?.status}).catch(()=>{});
  renderHR(); toast('Status updated');
}
</script>
