<script lang="ts">
    import {
        A,
        Heading,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
    } from "flowbite-svelte";
    import { ArrowRightOutline, CheckOutline } from "flowbite-svelte-icons";
    import Cards from "@C/LandingPage/CardsAnnouncement.svelte";
    import Navbar from "@C/LandingPage/Navbar.svelte";
    import Footer from "@C/LandingPage/Footer.svelte";
    import axiosInstance from "axios";
    import { onMount } from "svelte";

    const axios = axiosInstance.create();

    interface News {
        title: string;
        desc: string;
        attachment: string | null;
        created_at: string;
    }

    type NewsArray = News[];

    interface Civil {
        id: number;
        nik: string;
        fullName: string;
        birthplace: string;
        birthdate: number;
        residentstatus: string;
        nkk: string;
        isFamilyHead: number;
        rt_id: number;
        address: string;
        status: string;
        phone: string;
        religion: string;
        job: string;
        created_at: string;
        created_by: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
    }

    interface RT {
        id: number;
        leader_id: number | null;
        created_at: string;
        created_by: number;
        number: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
        civils: Civil[];
    }

    let dataRT: RT[] = [];
    let announcements: NewsArray = [];

    const getNews = async (): Promise<NewsArray> => {
        const response = await axios.get("/api/news/lts", {
            headers: {
                Accept: "application/json",
            },
        });

        // console.log("API News Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((item) => ({
                ...item,
                created_at: item.created_at.slice(0, 10), // Truncate date
            }));
        } else {
            throw new Error("Unexpected response format");
        }
    };

    const getRTData = async (): Promise<RT[]> => {
        const response = await axios.get("/api/rt", {
            headers: {
                Accept: "application/json",
            },
        });

        // console.log("API RT Data Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data;
        } else {
            throw new Error("Unexpected response format");
        }
    };

    onMount(async () => {
        try {
            announcements = await getNews();
            dataRT = await getRTData();
            // console.log(dataRT);
            // console.log(announcements);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    let pengumumans = [
        {
            img: "https://media.kompas.tv/library/image/content_article/article_img/20231204072833.jpg",
            title: "Perekrutan Anggota KPPS",
            desc: "Pengumuman bagi seluruh masyarakat, dengan adanya Pemilihan Umum yang akan segera dilaksanakan, bagi yang berminat untuk menjadi anggota KPPS untuk mengambil form pendaftaran di rumah Ketua RW 03.",
            date: "13 Maret 2024",
        },
        {
            img: "https://asset.kompas.com/crops/jyzXEDY9vOxsOPbXwe8vm8WpE4A=/34x0:704x447/750x500/data/photo/2021/07/27/60ffc24bc0c75.jpg",
            title: "Pengajuan Bantuan Sosial",
            desc: "Pengumuman bagi seluruh masyarakat, dengan dibukanya pengajuan bantuan sosial PKH, bagi yang ingin mengajukan Bantuan Sosial, dimohon untuk mengisi form pengajuan Bantuan Sosial",
            date: "13 Maret 2024",
        },
    ];
    let activitys = [
        {
            activityName: "Kerja Bakti",
            location: "Jl. Rambutan - Jl. Mangga",
            time: "08.00 - 10.00",
            date: "23 April 2024",
        },
        {
            activityName: "Rapat Karang Taruna",
            location: "Rumah Adit, Jl. Manggis No.8",
            time: "19.00 - 21.00",
            date: "24 April 2024",
        },
    ];
    let admins = [
        {
            name: "Wisnu",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Wisnu.jpg",
            head: "RW 03",
            position: "",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Subianto",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Subianta.jpg",
            head: "RW 03",
            position: "RT 01",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Jaya",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Jaya.jpg",
            head: "RW 03",
            position: "RT 02",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Gungun",
            photoProfile: "https://jatim.kemenkumham.go.id/images/gungun.jpg",
            head: "RW 03",
            position: "RT 03",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Indah",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Indah.jpg",
            head: "RW 03",
            position: "RT 04",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Ufi",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Ufi.jpg",
            head: "RW 03",
            position: "RT 05",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Ratno",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Ratno.jpg",
            head: "RW 03",
            position: "RT 06",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Meirina",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Meirina.jpg",
            head: "RW 03",
            position: "RT 07",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
        {
            name: "Adi",
            photoProfile: "https://jatim.kemenkumham.go.id/images/Adi.jpg",
            head: "RW 03",
            position: "RT 08",
            number: "085777123456",
            address: "Jl.Pahlawan No.510",
            greetings:
                "Salam sejahtera! Saya Wisnu, dengan keinginan kuat untuk membangun komunitas yang lebih baik dan harmonis bagi semua warga.",
        },
    ];
    let poins = [
        {
            title: "Pemantuan Proaktif",
            desc: "RW dapat melihat data penduduk, memvalidasi surat keterangan, dan menanggapi laporan warga secara efisien untuk menjaga ketertiban dan keamanan lingkungan.",
        },
        {
            title: "Informasi Terkini",
            desc: "RT dapat membuat kegiatan masyarakat, menetapkan iuran warga, dan mengirim pengumuman terkini kepada warga untuk memperkuat keterlibatan dan kebersamaan dalam komunitas",
        },
        {
            title: "Pelayanan Cepat",
            desc: "Warga dapat dengan mudah melaporkan masalah, mengajukan surat keterangan, serta melihat informasi dan pengumuman yang relevan dari RW atau RT untuk mendukung transparansi dan partisipasi aktif",
        },
        {
            title: "Kolaborasi Efektif",
            desc: "RT dan RW bekerja sama dalam mengubah data penduduk, membuat surat keterangan, serta menanggapi laporan warga untuk menciptakan lingkungan yang harmonis dan responsif terhadap kebutuhan masyarakat",
        },
    ];
</script>

<div class="relative px-8 md:px-16 mt-24 md:mt-0">
    <!--  -->
    <Navbar />

    <div class="content">
        <div
            class="hero grid grid-cols-1 md:grid-cols-2 gap-4 mb-32 min-h-screen"
        >
            <div class="flex flex-col items-center justify-center md:px-4">
                <img
                    src="assets/icons/KD_logo.svg"
                    alt=""
                    class="h-16 md:h-24 mb-5 self-start"
                />
                <Heading tag="h3" class="mb-4"
                    >Selamat Datang di Kawan Desa</Heading
                >
                <p>
                    Platform digital yang menghubungkan hati dan pikiran warga
                    desa dalam satu kesatuan harmonis. Di sini, kita bukan hanya
                    sekadar tetangga, tapi juga sahabat yang saling mendukung
                    dan memperkuat. Temukan segala informasi terkini seputar
                    kegiatan di lingkungan RT dan RW, mulai dari pengumuman
                    penting hingga agenda kebersamaan yang akan mempererat tali
                    persaudaraan di antara kita.
                </p>
                <p>
                    Jadilah bagian dari perubahan positif di desa kita. Ayo
                    bergabung dengan Kawan Desa, tempat di mana setiap suara
                    didengar, setiap langkah diperhitungkan, dan setiap mimpi
                    memiliki tempat untuk tumbuh bersama. Bersama Kawan Desa,
                    mari ciptakan desa yang menjadi kebanggaan kita semua!
                </p>
            </div>
            <div class="flex items-center justify-center">
                <div class="grid grid-cols-2 gap-4 p-12">
                    <img
                        src="https://static.promediateknologi.id/crop/0x0:0x0/0x0/webp/photo/p2/81/2023/07/19/kirab-budaya-balearjosari-harto-5-3783515462.jpg"
                        alt=""
                        class="rounded-lg object-none w-48 h-72 justify-self-end"
                    />
                    <img
                        src="https://assets.kompasiana.com/items/album/2021/07/02/img20210627092423-60de9c4706310e1b4024efb2.jpg?t=o&v=770"
                        alt=""
                        class="rounded-lg mt-4 object-none w-48 h-72"
                    />
                </div>
            </div>
        </div>

        <div class="announcement mb-24">
            <div class="text-center mb-8">
                <Heading tag="h3" class="mb-2">Pengumuman</Heading>
                <A
                    href="lp-pengumuman"
                    class="inline-block flex justify-center items-center"
                    >Lihat Selengkapnya &ensp; <ArrowRightOutline /></A
                >
            </div>
            <div class="group-card w-full">
                <!-- {#each pengumumans.slice(0, 2) as pengumuman}
                    <Cards
                        imageUrl={pengumuman.img}
                        hrefUrl="/"
                        classCard="mb-3 d-block bg-gray-100"
                        title={pengumuman.title}
                        description={pengumuman.desc}
                        date={pengumuman.date}
                    />
                {/each} -->
                {#each announcements as announcement}
                    <Cards
                        imageUrl={announcement.attachment ||
                            "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                        hrefUrl="/"
                        classCard="mb-3 d-block"
                        title={announcement.title}
                        description={announcement.desc}
                        date={announcement.created_at}
                    />
                {/each}
            </div>
        </div>
    </div>

    <div class="event-calendar mb-24">
        <div class="text-center mb-8">
            <Heading tag="h3" class="">Kalendar Acara</Heading>
        </div>
        <div class="calendar-table md:flex md:justify-center">
            <Table
                shadow
                hoverable={true}
                divClass="relative overflow-x-auto md:w-3/4"
            >
                <TableHead class="uppercase">
                    <TableHeadCell>Nama Kegiatan</TableHeadCell>
                    <TableHeadCell>Lokasi</TableHeadCell>
                    <TableHeadCell>Waktu</TableHeadCell>
                    <TableHeadCell>Tanggal</TableHeadCell>
                </TableHead>
                <TableBody tableBodyClass="divide-y">
                    {#each activitys as activity}
                        <TableBodyRow>
                            <TableBodyCell
                                >{activity.activityName}</TableBodyCell
                            >
                            <TableBodyCell>{activity.location}</TableBodyCell>
                            <TableBodyCell>{activity.time}</TableBodyCell>
                            <TableBodyCell>{activity.date}</TableBodyCell>
                        </TableBodyRow>
                    {/each}
                </TableBody>
            </Table>
        </div>
    </div>
    <div class="list-admins mb-24">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Daftar Pengurus RW 03</Heading>
            <A
                href="lp-profile"
                class="inline-block flex justify-center items-center"
                >Lihat Selengkapnya &ensp; <ArrowRightOutline /></A
            >
        </div>
        <div class="flex md:flex-row flex-col mb-10">
            <img
                src={admins[0].photoProfile}
                alt=""
                class="max-w-md md:w-4/5 object-cover rounded-lg max-h-96 object-top"
            />
            <div
                class="desc-profile p-4 md:p-8 md:flex md:justify-center md:align-center"
            >
                <div
                    class="w-4/5 md:flex md:flex-col md:justify-center md:align-center"
                >
                    <Heading tag="h4" class="mb-2">{admins[0].name}</Heading>
                    <Heading tag="h5" class="mb-2"
                        >Ketua {admins[0].head}</Heading
                    >
                    <p class="text-gray-500">{admins[0].greetings}</p>
                </div>
            </div>
        </div>
        <div
            class="list-rt grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-8"
        >
            <!-- {#each admins.slice(1) as admin}
                <div class="rounded-lg shadow-lg p-2 md:p-4">
                    <img
                        src={admin.photoProfile}
                        alt=""
                        class="w-full object-cover rounded-lg mb-3 max-h-64 object-top"
                    />
                    <Heading tag="h5" class="mb-2">{admin.name}</Heading>
                    <p class="text-gray-500 mb-2">
                        Ketua {admin.position} / {admin.head}
                    </p>
                </div>
            {/each} -->
            <!-- data from database -->
            {#each dataRT as rt}
                <div class="rounded-lg shadow-lg p-2 md:p-4">
                    <img
                        src={rt.civils[0]?.leader_id
                            ? rt.civils[0].leader_id
                            : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                        alt="Profile Image"
                        class="w-full object-cover rounded-lg mb-3 max-h-64 object-top"
                    />
                    <div class="mt-2">
                        <Heading tag="h5" class="mb-2"
                            >RT {rt.civils[0].fullName}</Heading
                        >
                        <p class="text-gray-500">
                            Ketua RT {rt.number} / RW {rt.leader_id || 3}
                        </p>
                    </div>
                </div>
            {/each}
        </div>
    </div>
    <div
        class="advantage grid grid-cols-1 lg:grid-cols-[40%_auto] items-center pb-24"
    >
        <div class="adv-text">
            <Heading tag="h3" class="mb-3">Keunggulan <br /> Kawan Desa</Heading
            >
            <p class="text-gray-500 lg:w-3/5">
                Platform kolaboratif dan inovatif untuk kemajuan desa, mendukung
                partisipasi aktif warga dengan layanan berkualitas
            </p>
        </div>
        <div
            class="point-adv grid md:grid-cols-2 grid-cols-1 mt-8 lg:mt-0 gap-4"
        >
            {#each poins as poin}
                <div>
                    <div class="p-2 bg-gray-100 inline-block rounded-xl">
                        <CheckOutline
                            strokeWidth="3"
                            size="lg"
                            class="font-bold text-blue-800"
                        />
                    </div>
                    <Heading tag="h4">{poin.title}</Heading>
                    <p class="text-gray-500">{poin.desc}</p>
                </div>
            {/each}
        </div>
    </div>
    <Footer />
</div>
