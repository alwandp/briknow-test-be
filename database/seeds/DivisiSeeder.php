<?php

use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Divisi::create([
            'cost_center'   => 'NEW1',
            'direktorat'    => 'Micro Business Directorate',
            'divisi'        => 'Ultra Micro Business Team Division',
            'shortname'     => 'UMI',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW2',
            'direktorat'    => 'Small, Retail & Medium Business Directorate',
            'divisi'        => 'Medium Business 1 Division',
            'shortname'     => 'MBO',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW3',
            'direktorat'    => 'Small, Retail & Medium Business Directorate',
            'divisi'        => 'Medium Business 2 Division',
            'shortname'     => 'MBT',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW4',
            'direktorat'    => 'Network & Service Directorate',
            'divisi'        => 'Distribution Network Division',
            'shortname'     => 'DNR',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW5',
            'direktorat'    => 'Digital & Information Technology Directorate',
            'divisi'        => 'Enterprise Data Management Division',
            'shortname'     => 'EDM',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW6',
            'direktorat'    => 'Human Capital Directorate',
            'divisi'        => 'Corporate University Division',
            'shortname'     => 'BCU',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW7',
            'direktorat'    => 'Internal Audit Directorate',
            'divisi'        => 'Head Office, Special Branch & Overseas Network Audit Division',
            'shortname'     => 'AIK',
        ]);
        \App\Divisi::create([
            'cost_center'   => 'NEW8',
            'direktorat'    => 'Internal Audit Directorate',
            'divisi'        => 'Information Technology Audit Desk Division',
            'shortname'     => 'AIT',
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20024',
            'direktorat'  => 'Change Management & Transformation Office Directorate',
            'divisi'      => 'Desk Project Management Office Division',
            'shortname'   => 'PMO'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10007',
            'direktorat'  => 'Compliance Directorate',
            'divisi'      => 'Kebijakan & Prosedur Division',
            'shortname'   => 'KPD'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10008',
            'direktorat'  => 'Risk Management Directorate',
            'divisi'      => 'Kebijakan Risiko Kredit Division',
            'shortname'   => 'KRD'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10009',
            'direktorat'  => 'Risk Management Directorate',
            'divisi'      => 'Risk Enterprise & Mnjm Portofolio Division',
            'shortname'   => 'EMP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10010',
            'direktorat'  => 'Risk Management Directorate',
            'divisi'      => 'Manajemen Risk Oprasional & Pasar Division',
            'shortname'   => 'MOP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10011',
            'direktorat'  => 'Finance Directorate',
            'divisi'      => 'Assets Liabilities Management Division',
            'shortname'   => 'ALM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10012',
            'direktorat'  => 'Finance Directorate',
            'divisi'      => 'Hubungan Investor Division',
            'shortname'   => 'DHI'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10014',
            'direktorat'  => 'Corporate Banking Directorate',
            'divisi'      => 'Sindikasi & Jasa Lembaga Keuangan Division',
            'shortname'   => 'SJK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10015',
            'direktorat'  => 'Network & Service Directorate',
            'divisi'      => 'Operasional Kredit Division',
            'shortname'   => 'OPK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10017',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Retail Payment Division',
            'shortname'   => 'RPT'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10019',
            'direktorat'  => 'Digital & Information Technology Directorate',
            'divisi'      => 'Digital Center Of Excellence Division',
            'shortname'   => 'DCE'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10024',
            'direktorat'  => 'Network & Services',
            'divisi'      => 'Desk Jaringan Brilink Division',
            'shortname'   => 'BND'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS10029',
            'direktorat'  => 'Digital & Information Technology Directorate',
            'divisi'      => 'Desk Information Security Division',
            'shortname'   => 'ISC'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19001',
            'direktorat'  => 'Human Capital Directorate',
            'divisi'      => 'Human Capital Business Partner Division',
            'shortname'   => 'HCBP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19002',
            'direktorat'  => 'Human Capital Directorate',
            'divisi'      => 'Human Capital Development Division',
            'shortname'   => 'HCD'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19003',
            'direktorat'  => 'Human Capital Directorate',
            'divisi'      => 'Human Capital Strategy & Policy Division',
            'shortname'   => 'HCS'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19005',
            'direktorat'  => 'Human Capital Directorate',
            'divisi'      => 'Culture Transformation Division',
            'shortname'   => 'CTR'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19006',
            'direktorat'  => 'Micro Business Directorate',
            'divisi'      => 'Micro Sales Management Division',
            'shortname'   => 'MSM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19007',
            'direktorat'  => 'Micro Business Directorate',
            'divisi'      => 'Kebijakan Bisnis Mikro Division',
            'shortname'   => 'KBM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19009',
            'direktorat'  => 'Micro Business Directorate',
            'divisi'      => 'Social Entrepreneurship & Inkubasi Division',
            'shortname'   => 'SEI'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19011',
            'direktorat'  => 'Small, Retail & Medium Business Directorate',
            'divisi'      => 'Kebijakan Bisnis Kecil, Ritel & Menengah Division',
            'shortname'   => 'BKRM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19012',
            'direktorat'  => 'Small, Retail & Medium Business Directorate',
            'divisi'      => 'Small Sales Management Division',
            'shortname'   => 'MSM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19013',
            'direktorat'  => 'Institutional & SOE Directorate',
            'divisi'      => 'Institutional Division',
            'shortname'   => 'INS'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19014',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Mass Funding Division',
            'shortname'   => 'MFD'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19017',
            'direktorat'  => 'Network & Service Directorate',
            'divisi'      => 'Jaringan Brilink Division',
            'shortname'   => 'BND'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19018',
            'direktorat'  => 'Digital & Information Technology Directorate',
            'divisi'      => 'It Strategy & Governance Division',
            'shortname'   => 'ISG'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19019',
            'direktorat'  => 'Digital & Information Technology Directorate',
            'divisi'      => 'Application Management & Operation Division',
            'shortname'   => 'APP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19020',
            'direktorat'  => 'Digital & Information Technology Directorate',
            'divisi'      => 'It Infrastructure & Operation Division',
            'shortname'   => 'INF'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS19023',
            'direktorat'  => 'Finance Directorate',
            'divisi'      => 'Tim Implementasi Bri Financial Enterprise System Division',
            'shortname'   => 'NFS'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20001',
            'direktorat'  => 'Institutional & SOE Directorate',
            'divisi'      => 'SOE 1 Division',
            'shortname'   => 'SOO'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20002',
            'direktorat'  => 'Institutional & SOE Directorate',
            'divisi'      => 'SOE 2 Division',
            'shortname'   => 'SOT'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20004',
            'direktorat'  => 'Corporate Banking Directorate',
            'divisi'      => 'Corporate Banking 1 Division',
            'shortname'   => 'CBO'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20005',
            'direktorat'  => 'Corporate Banking Directorate',
            'divisi'      => 'Corporate Banking 2 Division',
            'shortname'   => 'CBT'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20007',
            'direktorat'  => 'Fixed Assets Management & Procurement Directorate',
            'divisi'      => 'Procurement, Logistic Policy & Fix Assets Management Division',
            'shortname'   => 'PLM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS20008',
            'direktorat'  => 'Fixed Assets Management & Procurement Directorate',
            'divisi'      => 'Procurement & Logistic Operation Division',
            'shortname'   => 'PLO'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS52000',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Kartu Kredit Division',
            'shortname'   => 'KKD'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS53000',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Kredit Konsumer Division',
            'shortname'   => 'KRK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS54000',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Marketing Communication Division',
            'shortname'   => 'MCM'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS57000',
            'direktorat'  => 'Finance Directorate',
            'divisi'      => 'Corporate Development & Strategy Division',
            'shortname'   => 'CDS'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS58000',
            'direktorat'  => 'Treasury & Global Services Directorate',
            'divisi'      => 'Investment Service Division',
            'shortname'   => 'INV'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS59000',
            'direktorat'  => 'Consumer Directorate',
            'divisi'      => 'Wealth Management Division',
            'shortname'   => 'WMG'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS60000',
            'direktorat'  => 'Institutional & SOE Directorate',
            'divisi'      => 'Transaction Banking Division',
            'shortname'   => 'TRB'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS61000',
            'direktorat'  => 'Compliance Directorate',
            'divisi'      => 'Kepatuhan Division',
            'shortname'   => 'KEP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS70099',
            'direktorat'  => 'Internal Audit Directorate',
            'divisi'      => 'Satuan Kerja Audit Intern Division',
            'shortname'   => 'SKAI'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS71000',
            'direktorat'  => NULL,
            'divisi'      => 'Sekretariat Perusahaan Division',
            'shortname'   => 'SKP'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS79000',
            'direktorat'  => 'Risk Management Directorate',
            'divisi'      => 'Analisis Resiko Kredit Division',
            'shortname'   => 'ARK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS80000',
            'direktorat'  => 'Risk Management Directorate',
            'divisi'      => 'Restruktrs. & Penyelesaian Kredit Division',
            'shortname'   => 'RPK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS82000',
            'direktorat'  => 'Treasury & Global Services Directorate',
            'divisi'      => 'Treasury Business Division',
            'shortname'   => 'TRY'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS83000',
            'direktorat'  => 'Finance Directorate',
            'divisi'      => 'Akuntansi Manajemen & Keuangan Division',
            'shortname'   => 'AMK'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS84000',
            'direktorat'  => 'Treasury & Global Services',
            'divisi'      => 'Bisnis Internasional Division',
            'shortname'   => 'INT'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS85000',
            'direktorat'  => 'Network & Service Directorate',
            'divisi'      => 'Sentra Operasi Division',
            'shortname'   => 'STO'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS92000',
            'direktorat'  => 'Compliance Directorate',
            'divisi'      => 'Hukum Division',
            'shortname'   => 'LGL'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS98502',
            'direktorat'  => 'Network & Service Directorate',
            'divisi'      => 'Layanan & Contact Center Division',
            'shortname'   => 'LCC'
        ]);
        \App\Divisi::create([
            'cost_center' => 'PS98600',
            'direktorat'  => 'Change Management & Transformation Office Directorate',
            'divisi'      => 'Corporate Transformation Division',
            'shortname'   => 'CTF'
        ]);       

        // divisi udah engga ada 
        // tetap di input karna buat penyocokan data dengan bristar 
        
        \App\Divisi::create([
            'cost_center' => 'PS51201',
            'direktorat'  => NULL,
            'divisi'      => 'Pengembangan Bisnis E-banking',
            'shortname'   => 'EBanking'
        ]);
        
        \App\Divisi::create([
            'cost_center' => 'PS64000',
            'direktorat'  => NULL,
            'divisi'      => 'Desk E-Channel',
            'shortname'   => 'ECH'
        ]);
        
        \App\Divisi::create([
            'cost_center' => 'PS98200',
            'direktorat'  => NULL,
            'divisi'      => 'Direksi',
            'shortname'   => 'OPT'
        ]);

        
        \App\Divisi::create([
            'cost_center' => 'PS51000',
            'direktorat'  => NULL,
            'divisi'      => 'Dana & Jasa',
            'shortname'   => 'DANA&JASA'
        ]);

        
        \App\Divisi::create([
            'cost_center' => 'PS77000',
            'direktorat'  => NULL,
            'divisi'      => 'Bisnis Korporasi',
            'shortname'   => 'BKO'
        ]);

        
        \App\Divisi::create([
            'cost_center' => 'PS10032',
            'direktorat'  => NULL,
            'divisi'      => 'Bisnis Pertanian',
            'shortname'   => 'BPT'
        ]);

        
        \App\Divisi::create([
            'cost_center' => 'PS10025',
            'direktorat'  => NULL,
            'divisi'      => 'Bisnis Retail',
            'shortname'   => 'BRL'
        ]);
                
        \App\Divisi::create([
            'cost_center' => 'PS19010',
            'direktorat'  => NULL,
            'divisi'      => 'Bisnis Ritel & Menengah',
            'shortname'   => 'BRM'
        ]);
                
        \App\Divisi::create([
            'cost_center' => 'PS19004',
            'direktorat'  => NULL,
            'divisi'      => 'Human Capital Partnership Management',
            'shortname'   => 'HCM'
        ]);
                
        \App\Divisi::create([
            'cost_center' => 'PS50100',
            'direktorat'  => NULL,
            'divisi'      => 'Institution 1',
            'shortname'   => 'INS'
        ]);
                
        \App\Divisi::create([
            'cost_center' => 'PS50200',
            'direktorat'  => NULL,
            'divisi'      => 'Institution 2',
            'shortname'   => 'IND'
        ]);
                
        \App\Divisi::create([
            'cost_center' => 'PS10013',
            'direktorat'  => NULL,
            'divisi'      => 'Jaringan Bisnis Mikro',
            'shortname'   => 'JBM'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS10021',
            'direktorat'  => NULL,
            'divisi'      => 'Jaringan Bisnis Retail',
            'shortname'   => 'JBR'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS87000',
            'direktorat'  => NULL,
            'divisi'      => 'Kebijakan & Pengembangan HC',
            'shortname'   => 'KHC'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS10028',
            'direktorat'  => NULL,
            'divisi'      => 'Kerjasama Teknologi',
            'shortname'   => 'KJT'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS19015',
            'direktorat'  => NULL,
            'divisi'      => 'Kredit Briguna',
            'shortname'   => 'KBG'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS88200',
            'direktorat'  => NULL,
            'divisi'      => 'Management AT & Pengadaan Properti',
            'shortname'   => 'MAT'
        ]);

        \App\Divisi::create([
            'cost_center' => 'PS63000',
            'direktorat'  => NULL,
            'divisi'      => 'Perencanaan & Pengembangan IT',
            'shortname'   => 'PPT'
        ]);
    }
}