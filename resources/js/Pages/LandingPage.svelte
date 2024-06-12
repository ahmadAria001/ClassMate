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
        Popover,
    } from "flowbite-svelte";
    import {
        ArrowRightOutline,
        CheckOutline,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";
    import Cards from "@C/LandingPage/CardsAnnouncement.svelte";
    import Navbar from "@C/LandingPage/Navbar.svelte";
    import Footer from "@C/LandingPage/Footer.svelte";
    import axiosInstance from "axios";
    import { onMount } from "svelte";

    import { page } from "@inertiajs/svelte";

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
    let currentRW: any;
    let events: any;

    const getNews = async (): Promise<NewsArray> => {
        const response = await axios.get("/api/news/lts", {
            headers: {
                Accept: "application/json",
                lct: $page.props.location,
            },
        });

        // console.log("API News Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((item: any) => ({
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

    const getRW = async () => {
        try {
            const response = await axios.get(`/api/user/rw/current`);
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const getCitizenEvents = async () => {
        const response = await axios.get(`/api/docs/activity/lts`);

        return response.data;
    };

    onMount(async () => {
        try {
            announcements = await getNews();
            dataRT = await getRTData();
            currentRW = (await getRW()).data;
            events = (await getCitizenEvents()).data;
            // console.log(dataRT);
            // console.log(announcements);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

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

    const dateFormatter = (epoc: number) => {
        const date = new Date(epoc);

        const monthNames = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];

        const day = date.getDate();

        const monthIndex = date.getMonth();
        const monthName = monthNames[monthIndex];

        const year = date.getFullYear();

        return `${day} ${monthName} ${year}`;
    };
</script>

<Navbar />
<div class="relative px-8 md:px-16 mt-24 md:mt-0">
    <!--  -->

    {#if currentRW && dataRT && events && announcements}
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
                        Platform digital yang menghubungkan hati dan pikiran
                        warga desa dalam satu kesatuan harmonis. Di sini, kita
                        bukan hanya sekadar tetangga, tapi juga sahabat yang
                        saling mendukung dan memperkuat. Temukan segala
                        informasi terkini seputar kegiatan di lingkungan RT dan
                        RW, mulai dari pengumuman penting hingga agenda
                        kebersamaan yang akan mempererat tali persaudaraan di
                        antara kita.
                    </p>
                    <p>
                        Jadilah bagian dari perubahan positif di desa kita. Ayo
                        bergabung dengan Kawan Desa, tempat di mana setiap suara
                        didengar, setiap langkah diperhitungkan, dan setiap
                        mimpi memiliki tempat untuk tumbuh bersama. Bersama
                        Kawan Desa, mari ciptakan desa yang menjadi kebanggaan
                        kita semua!
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
                    {#each announcements as announcement}
                        <Cards
                            imageUrl={announcement.attachment
                                ? `/storage/assets/uploads/${announcement.attachment}`
                                : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
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
                        {#each events as evt}
                            <TableBodyRow>
                                <TableBodyCell>
                                    <div class="flex">
                                        <p class="w-full truncate max-w-32">
                                            {evt.name}
                                        </p>
                                        <QuestionCircleSolid
                                            id={`name-${evt.id}`}
                                        />
                                        <Popover
                                            class="w-64 text-sm text-black dark:text-white z-50"
                                            title="Deskripsi"
                                            triggeredBy={`#name-${evt.id}`}
                                        >
                                            {evt.name}
                                        </Popover>
                                    </div>
                                </TableBodyCell>
                                <TableBodyCell>
                                    <div class="flex">
                                        <p class="w-full truncate max-w-36">
                                            {evt.location}
                                        </p>
                                        <QuestionCircleSolid
                                            id={`loct-${evt.id}`}
                                        />
                                        <Popover
                                            class="w-64 text-sm text-black dark:text-white z-50"
                                            title="Deskripsi"
                                            triggeredBy={`#loct-${evt.id}`}
                                        >
                                            {evt.location}
                                        </Popover>
                                    </div>
                                </TableBodyCell>
                                <TableBodyCell>
                                    {new Date(
                                        evt.startDate * 1000,
                                    ).toLocaleTimeString(undefined, {
                                        hour12: false,
                                    })} - {new Date(
                                        evt.endDate * 1000,
                                    ).toLocaleTimeString(undefined, {
                                        hour12: false,
                                    })}
                                </TableBodyCell>
                                <TableBodyCell>
                                    {dateFormatter(evt.startDate * 1000)} - {dateFormatter(
                                        evt.endDate * 1000,
                                    )}</TableBodyCell
                                >
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
                    src={currentRW.pict
                        ? `/storage/assets/uploads/${currentRW.pict}`
                        : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                    alt=""
                    class="max-w-md md:w-4/5 object-cover rounded-lg max-h-96 object-top"
                />
                <div
                    class="desc-profile p-4 md:p-8 md:flex md:justify-center md:align-center"
                >
                    <div
                        class="w-4/5 md:flex md:flex-col md:justify-center md:align-center"
                    >
                        <Heading tag="h4" class="mb-2"
                            >{currentRW.civilian_id.fullName}</Heading
                        >
                        <Heading tag="h5" class="mb-2">Ketua RW 3</Heading>
                        <p class="text-gray-500">
                            {currentRW.intro ? currentRW.intro : ""}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="list-rt grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-8"
            >
                {#each dataRT as rt}
                    <div class="rounded-lg shadow-lg p-2 md:p-4">
                        <img
                            src={rt.leader_id?.pict
                                ? `/storage/assets/uploads/${rt.leader_id?.pict}`
                                : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                            alt="Profile Image"
                            class="w-full object-cover rounded-lg mb-3 max-h-64 object-top"
                        />
                        <div class="mt-2">
                            <Heading tag="h5" class="mb-2"
                                >RT {rt.leader_id
                                    ? rt.leader_id?.civilian_id.fullName
                                    : ""}</Heading
                            >
                            <p class="text-gray-500">
                                Ketua RT {rt.number}
                                <!-- / RW {rt.leader_id || 3} -->
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
                <Heading tag="h3" class="mb-3"
                    >Keunggulan <br /> Kawan Desa</Heading
                >
                <p class="text-gray-500 lg:w-3/5">
                    Platform kolaboratif dan inovatif untuk kemajuan desa,
                    mendukung partisipasi aktif warga dengan layanan berkualitas
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
    {/if}
    <Footer />
</div>
