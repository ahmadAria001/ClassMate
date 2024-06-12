<script lang="ts">
    import { Heading, Select } from "flowbite-svelte";
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

    let announcements: NewsArray = [];

    const getNews = async (): Promise<NewsArray> => {
        const response = await axios.get("/api/news/", {
            headers: {
                Accept: "application/json",
            },
        });

        console.log("API Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((item) => ({
                ...item,
                created_at: item.created_at.slice(0, 10), // ngecut date
            }));
        } else {
            throw new Error("Unexpected response format");
        }
    };

    onMount(async () => {
        try {
            announcements = await getNews();
            // console.log(announcements);
        } catch (error) {
            console.error("Error fetching news:", error);
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

    let filterTime = [
        { value: "7-day", name: "7 Hari Terakhir" },
        { value: "1-month", name: "1 Bulan Terakhir" },
        { value: "1-year", name: "1 Tahun Terakhir" },
    ];
    let selected;
</script>

<div class="relative px-8 md:px-16 mt-24">
    <Navbar />

    <div class="content">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Pengumuman</Heading>
        </div>

        <div class="w-full mb-8 grid">
            <Select
                id="filter"
                bind:value={selected}
                class="w-40 shadow-lg justify-self-end"
                size="sm"
            >
                <option selected value="7-day">7 Hari Terakhir</option>
                {#each filterTime as { value, name }}
                    <option {value}>{name}</option>
                {/each}
            </Select>
        </div>

        <div class="group-card w-full pb-24">
            {#each announcements as announcement}
                <Cards
                    imageUrl={announcement.attachment ||
                        "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                    hrefUrl="/"
                    classCard="mb-3 d-block bg-gray-100"
                    title={announcement.title}
                    description={announcement.desc}
                    date={announcement.created_at}
                />
            {/each}
        </div>
    </div>

    <Footer />
</div>
