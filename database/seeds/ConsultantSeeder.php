<?php

use Illuminate\Database\Seeder;

class ConsultantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Consultant::create([
            'nama'          => 'PT BRINGIN INTI TEKNOLOGI',
            'tentang'       => 'PT Bringin Inti Teknologi Atau BRIIT adalah anak perusahaan dana pensiunan BRI (DAPEN BRI) yang banyak bergerak dalam bidang IT Solution',
            'bidang'        => 'IT Solution',
            'website'       => 'https://www.briit.co.id/website/index.html',
            'telepon'       => '02157906373',
            'email'         => 'corp@briit.co.id',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Jl. Tanah Abang IV No.32h, RT.4/RW.3, Dukuh Atas, RT.3/RW.3, Petojo Sel., Kecamatan Gambir, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10160'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT ITOMMEY BINTANG INDONESIA',
            'tentang'       => "Companies are moving towards to digital era. With that, IT Organizations are racing to be the enabler for all digital services supporting the business. We are here to help companies shape their IT Organization to be ready to adapt all the technologies required for digital transformation. We're here not only to be your vendor, but we will be your trustworthy professional partner and your reinforcement to face all the challanges in digital technology. We're equipped with experienced professional people, great technologies also strong with knowledge of IT digital architecture and process.",
            'bidang'        => 'IT Solution',
            'website'       => 'https://www.itommey.com/',
            'telepon'       => '02129826060',
            'email'         => 'info@itommey.com',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Menara Cardig, Lantai Mezzanine Jl. Raya Halim Perdanakusuma Jakarta 13650 Indonesia'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT INDUSTRI TELEKOMUNIKASI INDONESIA',
            'tentang'       => "PT INTI (Persero), one of the state-owned enterprises in strategic industries, was officially established on December 30, 1974. The Company headquartered in Jalan Moch Toha No. 77 Bandung has portfolio in the fields of Manufacture and Assembly, Managed Service, Digital Service, and System Integrator. To support its business, PT INTI (Persero) also operates an eight hectares production facility on Jalan Moch Toha No 225 which produces telecommunications and electronic devices.",
            'bidang'        => 'Manufacture and Assembly, Managed Service, Digital Service, and System Integrator',
            'website'       => 'https://www.inti.co.id/',
            'telepon'       => '0225206510',
            'email'         => 'info@inti.co.id',
            'facebook'      => 'https://id-id.facebook.com/ptintiofficial/',
            'instagram'     => 'https://www.instagram.com/ptintiofficial/',
            'lokasi'        => 'Gedung Graha Pratama Lantai 11 Jl. Letjen. MT. Haryono Kav. 15, Tebet Jakarta Selatan 12810'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT LEN INDUSTRI',
            'tentang'       => 'PT Len Industri (Persero) saat ini berada di bawah koordinasi Kementrian Negara BUMN dengan kepemilikan saham 100% oleh Pemerintah Republik Indonesia. Selama ini, Len telah mengembangkan bisnis dan produk-produk dalam bidang elektronika untuk industri dan prasarana',
            'bidang'        => 'Technology',
            'website'       => 'https://www.len.co.id/',
            'telepon'       => '0225202682',
            'email'         => 'marketing@len.co.id',
            'facebook'      => 'https://www.facebook.com/LenIndustri',
            'instagram'     => 'https://www.instagram.com/lenindustri/',
            'lokasi'        => 'Jl. Raya Subang - Cikamurang Km 12 Cibogo, Subang 41285 - Jawa Barat'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT TELKOM INDONESIA',
            'tentang'       => 'PT Telkom Indonesia Tbk, biasa disebut Telkom Indonesia atau Telkom saja adalah perusahaan informasi dan komunikasi serta penyedia jasa dan jaringan telekomunikasi secara lengkap di Indonesia.',
            'bidang'        => 'Informasi & Komunikasi',
            'website'       => 'https://www.telkom.co.id/sites',
            'telepon'       => '02180863539',
            'email'         => 'corporate_comm@telkom.co.id',
            'facebook'      => 'https://www.facebook.com/TelkomIndonesia',
            'instagram'     => 'https://www.instagram.com/telkomindonesia/',
            'lokasi'        => 'Telkom Landmark Tower, 39-nd floor Jl. Jendral Gatot Subroto Kav. 52 RT.6/RW.1, Kuningan Barat, Mampang Prapatan Jakarta Selatan, DKI Jakarta, 12710 Indonesia'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT SATKOMINDO MEDIYASA',
            'tentang'       => 'PT Satkomindo Mediyasa adalah penyedia solusi infrastruktur jaringan telekomunikasi terkemuka. Kami adalah anak perusahaan dari Dana Pensiun BRI (Bank Rakyat Indonesia). Kami menyediakan layanan telekomunikasi berbasis satelit yang dikenal sebagai VSAT (Very Small Aperture Terminal) dan juga layanan terestrial.

            Sebagai penyedia layanan solusi jaringan, SATKOMINDO menawarkan total layanan untuk kebutuhan perusahaan dalam jaringan telekomunikasi, mulai dari identifikasi kebutuhan jaringan, pengukuran pola lalu lintas, perhitungan kebutuhan bandwidth, desain jaringan, rencana implementasi, peluncuran instalasi, integrasi jaringan, pemantauan dan pengendalian, hingga jaringan pemeliharaan.
            
            Didukung oleh tim profesional dan berpengalaman kami di lapangan bersama dengan Service Point yang tersebar luas di seluruh Indonesia, SATKOMINDO siap memberikan solusi dan layanan telekomunikasi terbaik untuk perusahaan Anda dalam hal data, suara, komunikasi video serta akses internet.
            Bermaksud untuk melakukan rekuitmen tenaga programmer yang akan ditempatkan di Bank tersebut.',

            'bidang'        => 'infrastruktur jaringan telekomunikasi',
            'website'       => 'https://www.satkomindo.com/#home',
            'telepon'       => '02129125062',
            'email'         => 'marketing@brinetcom.com',
            'facebook'      => 'https://www.facebook.com/BRINETCOM/?ref=ts&fref=ts',
            'instagram'     => '-',
            'lokasi'        => 'Jl.RS.Fatmawati No.1, Cilandak Barat. Jakarta Selatan. 12430'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT DELOITTE',
            'tentang'       => 'Deloitte is a leading global provider of audit and assurance, consulting, financial advisory, risk advisory, tax, and related services. During its 175-year history, our organization has grown tremendously in both scale and capabilities. Deloitte currently has approximately 330,000 people in more than 150 countries and territories, and serves four out of five Fortune Global 500® companies. Yet, our shared culture and mission—to make an impact that matters—remains unchanged. This is evident not only in Deloitte’s work for clients, but also in our WorldClass ambition, our WorldClimate initiative and our ALL IN diversity and inclusion strategy.',
            'bidang'        => 'Audit, Consulting, Advisory, and Tax Services',
            'website'       => 'https://www2.deloitte.com/global/en.html',
            'telepon'       => '02150818000',
            'email'         => '-',
            'facebook'      => 'https://www.facebook.com/DeloitteIndonesia/',
            'instagram'     => 'https://www.instagram.com/deloitte_indonesia/',
            'lokasi'        => 'The Plaza Office Tower, 32nd Floor, Jl. M.H. Thamrin Kav 28-30, RT.9/RW.5, Gondangdia, Menteng, RT.9/RW.5, Gondangdia, Kec. Menteng, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10350'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT Somia Customer Experience',
            'tentang'       => 'Pengalaman Pelanggan Somia membantu perusahaan menemukan wawasan tentang pelanggan mereka dan membantu mereka mengubah solusi dan organisasi mereka. Perusahaan ini bergerak di bidang Data and Analytics, Design.',
            'bidang'        => 'jasa keamanan, jasa security, dan jasa satpam',
            'website'       => 'https://somiacx.com/',
            'telepon'       => '-',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Plaza Bapindo, Jl. Jend. Sudirman Kav. 54-55, Senayan, Kby. Baru, Jakarta, Daerah Khusus Ibukota Jakarta 12190, Indonesia'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT Sri Rejeki Isman Tbk',
            'tentang'       => 'We serve visits from schools, universities, companies and agencies who are interested in visiting our Head Office in Sukoharjo, Central Java. During this visit, you can get to know more about how a textile factory works, from spinning to becoming a garment. We are proud to be the only company in Indonesia that can demonstrate and educate the public about the work processes of the best textile factories in Indonesia. It’s easy, please read the terms and fill in the online form below, one of our General Affairs officers will call you back within 24-48 hours.',
            'bidang'        => 'Pabrik tekstil dan garmen',
            'website'       => 'https://www.sritex.co.id/',
            'telepon'       => '0271593188',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Jl. KH. Samanhudi 88, Jetis, Sukoharjo, Solo – Central Java Indonesia'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT Bolia Mitra Utama',
            'tentang'       => "PT Bolia Mitra Utama as a company focusing on promotional items targeting corporate segment. We Provide all kind of promotional item, merchandise/souvenir/gift that could be customized to fulfill clients' needs and request. Our Workshop is equipped with more than 50 sewing machine that can produced all garment items such as dolls, any bag provided more than 25.000 pieces per month. We also have designer that can help provide unique design as request.",
            'bidang'        => 'Promotion Item Berbagai Macam Gimmick.',
            'website'       => 'http://boliajaya.com/index.php',
            'telepon'       => '02154381676',
            'email'         => 'boliajaya@gmail.com',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Address : Jl. Raya Duri Kosambi No. 79 M - 79 L, Cengkareng - Duri Kosambi Jakarta 11750'
        ]);

        \App\Consultant::create([
            'nama'          => 'CV Maju Lestari',
            'tentang'       => 'Maju Lestari adalah perusahaan yang bergerak di bidang tekstil khususnya di bidang pakaian jadi atau garmen. Produk yang dibuat oleh perusahaan adalah pesanan dari pembeli. Beberapa pembeli yang bekerja sama dengan kami terus terjaga kualitasnya, karena yang paling penting adalah kepuasan pelanggan.',
            'bidang'        => 'bidang tekstil Pakaian & Garmen',
            'website'       => 'http://majulestarigarment.com/',
            'telepon'       => '0226122960',
            'email'         => 'md@maju-lestari.com',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Jl.katalina raya no.9, perumahan cendrawasih, Andir, Bandung – Jawa Barat – Indonesia.'
        ]);

        \App\Consultant::create([
            'nama'          => 'CV Surya Cipta Kreasi',
            'tentang'       => 'Kami bangga dalam budaya tim kami yang kuat saling percaya, belajar, berbagi, kepedulian dan perhatian. Termasuk menyediakan lingkungan kerja yang nyaman dan kondusif dan meningkatkan profesional dan pribadi pertumbuhan staf kami. Kami mencari professional muda yang termotivasi, dinamis, mandiri, kreatif, inovatif, bertanggung jawab dan disiplin bersedia untuk maju dan bergabung dengan kami dan menjadi salah satu tim yang hebat kami.',
            'bidang'        => 'Pabrik tekstil dan garmen',
            'website'       => 'http://www.suryaciptamandiri.co.id',
            'telepon'       => '02122302547',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Jalan Taman Palem Raya, Kota Jakarta Barat, Jakarta, Indonesia'
        ]);

        \App\Consultant::create([
            'nama'          => 'CV TEGUH JAYA ABADI',
            'tentang'       => 'CV. TEGUH JAYA ABADI adalah badan usaha berpengalaman yang mengerjakan proyek nasional. CV. TEGUH JAYA ABADI saat ini memiliki kualifikasi . CV. TEGUH JAYA ABADI dapat mengerjakan proyek-proyek dengan sub klasifikasi',
            'bidang'        => 'Kontruksi Bangunan',
            'website'       => 'https://indokontraktor.com/business/cv-teguh-jaya-abadi',
            'telepon'       => '0271593188',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Jl. Halim Perdana Kusuma No.19, RT.001/RW.005, Jurumudi, Benda, Tangerang kota, Banten 15124'
        ]);

        \App\Consultant::create([
            'nama'          => 'Accenture',
            'tentang'       => 'Accenture plc is an Irish-domiciled multinational company that provides consulting and processing services. A Fortune Global 500 company, it reported revenues of $44.33 billion in 2020 and had 537,000 employees. In 2015, the company had about 150,000 employees in India, 48,000 in the US, and 50,000 in the Philippines.',
            'bidang'        => 'Layanan dan Teknologi Informasi',
            'website'       => 'https://www.accenture.com/id-en',
            'telepon'       => '-',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Lingkaran Syed Putra Kuala Lumpur, Federal Territory of Kuala Lumpur 59200, MY'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT Infosys',
            'tentang'       => 'Infosys Limited is an Indian multinational information technology company that provides business consulting, information technology and outsourcing services. The company was founded in Pune and is headquartered in Bangalore',
            'bidang'        => 'Information Technology',
            'website'       => 'https://www.infosys.com/',
            'telepon'       => '+61398602000',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'Two Melbourne Quarter Level 4, 697 Collins Street Docklands, 3008 VIC'
        ]);

        \App\Consultant::create([
            'nama'          => 'PT Karim Consulting Indonesia',
            'tentang'       => 'KARIM Consulting Indonesia is a dynamic consulting firm specializing in Islamic Economics and Finance supported by professional people working full time. KARIM Consulting Indonesia was established in August 2001 and positioned itself as a world leading Shariah Compliance consulting firm. We continuously pursue the search for innovative products and present new concepts in Islamic Banking and Finance through publications and free sessions. KARIM Consulting Indonesia believes that to further develop and promote Islamic Banking and Finance, trainings in Islamic Banking and Finance area are essential. KARIM Consulting Indonesia believes that the development of the human potential is very much needed. We, at KARIM Consulting Indonesia, help pioneer Islamic Thought through our activities at major Universities in Indonesia. We work closely with the research Teams in these Universities to develop new analytical tools and methods to be applied in the development of new Islamic Banking and Finance instruments and provide the relevant research base for Islamic Economics publications.',
            'bidang'        => 'Islamic Economics and Finance',
            'website'       => 'https://www.compnet.co.id',
            'telepon'       => '02175917891',
            'email'         => '-',
            'facebook'      => '-',
            'instagram'     => '-',
            'lokasi'        => 'AKR TOWER Gallery West Office Tower 8th floor Jl. Panjang No. 5, Kebon Jeruk Jakarta Barat 11530 - Indonesia  '
        ]);
    }
}
