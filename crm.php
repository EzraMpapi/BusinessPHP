<div class="space-y-4">
  <div class="section-header">
    <h2>CRM Pipeline</h2>
    <button class="btn btn-primary" onclick="openLeadForm()">
      <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Lead
    </button>
  </div>

  <!-- KPIs -->
  <div id="crm-kpis" class="grid-3" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div>
  </div>

  <!-- Leads table -->
  <div id="leads-table"></div>

  <!-- Add Lead Modal -->
  <div id="lead-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">New Lead</h3>
        <button onclick="closeLeadForm()" class="text-slate-400 hover:text-[#111827]">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <div class="space-y-3">
        <div>
          <label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Company *</label>
          <input id="f-company" class="bs-input" placeholder="Company name" />
        </div>
        <div>
          <label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Contact Person *</label>
          <input id="f-contact" class="bs-input" placeholder="Full name" />
        </div>
        <div>
          <label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Deal Value (<?= CURRENCY ?>)</label>
          <input id="f-value" type="number" class="bs-input" placeholder="e.g. 5000000" />
        </div>
        <div>
          <label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Stage</label>
          <select id="f-stage" class="bs-input">
            <option>Prospecting</option><option>Qualified</option>
            <option>Proposal</option><option>Negotiation</option><option>Won</option>
          </select>
        </div>
        <div>
          <label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Email</label>
          <input id="f-email" type="email" class="bs-input" placeholder="contact@company.com" />
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveLead()">Save Lead</button>
          <button class="btn btn-secondary" onclick="closeLeadForm()">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let leads = [];

(async function() {
  const rows = await fetchRows('crm_leads', {}, 200);
  leads = rows.length ? rows : window.SEED.leads;
  renderCRM();
})();

function renderCRM() {
  const total = leads.reduce((s,l)=>s+(l.value||l.deal_value||0),0);
  const won   = leads.filter(l=>l.stage==='Won').reduce((s,l)=>s+(l.value||l.deal_value||0),0);
  const avg   = leads.length ? total/leads.length : 0;

  document.getElementById('crm-kpis').innerHTML =
    buildKpi('Total Pipeline', money(total), '', null, '#2563EB') +
    buildKpi('Won This Month', money(won), '+18%', true, '#16A34A') +
    buildKpi('Avg Deal Size', money(avg), '', null, '#7C3AED');

  document.getElementById('leads-table').innerHTML = buildTable(
    ['Company','Contact','Value','Stage','Email',''],
    leads,
    l => `<tr>
      <td class="font-semibold text-[#111827]">${l.company||l.company_name||''}</td>
      <td class="text-slate-500">${l.contact||l.contact_person||''}</td>
      <td class="font-bold">${money(l.value||l.deal_value)}</td>
      <td>${buildPill(l.stage, window.STAGE_COLOR[l.stage]||'#94A3B8')}</td>
      <td class="text-slate-500">${l.email||''}</td>
      <td><button class="text-slate-400 hover:text-red-500 transition-colors" onclick="deleteLead(${l.id})">
        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i></button></td>
    </tr>`
  );
  lucide.createIcons();
}

function openLeadForm() {
  document.getElementById('lead-modal').classList.remove('hidden');
}
function closeLeadForm() {
  document.getElementById('lead-modal').classList.add('hidden');
}

async function saveLead() {
  const company = document.getElementById('f-company').value.trim();
  const contact = document.getElementById('f-contact').value.trim();
  if (!company || !contact) { toast('Company and contact are required', 'error'); return; }

  const row = {
    company_name: company, contact_person: contact,
    deal_value: Number(document.getElementById('f-value').value) || 0,
    stage: document.getElementById('f-stage').value,
    email: document.getElementById('f-email').value.trim(),
  };

  try {
    await insertRow('crm_leads', row);
    toast('Lead saved to Supabase!');
  } catch (e) {
    // Offline / not connected — add to local list
    leads.unshift({ id: uid(), company: company, contact: contact,
      value: row.deal_value, stage: row.stage, email: row.email });
    toast('Lead added (offline mode)');
  }

  closeLeadForm();
  renderCRM();
}

async function deleteLead(id) {
  if (!confirm('Delete this lead?')) return;
  leads = leads.filter(l => l.id !== id);
  try { await deleteRow('crm_leads', id); } catch(e) {}
  renderCRM();
  toast('Lead deleted');
}
</script>
