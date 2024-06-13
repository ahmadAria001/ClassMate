<script lang="ts">
    import { Heading, Select } from "flowbite-svelte";
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

    let announcements: NewsArray = [];

    const getNews = async (): Promise<NewsArray> => {
        const response = await axios.get("/api/news/", {
            headers: {
                Accept: "application/json",
                lct: $page.props.location,
            },
        });

        // console.log("API Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((item: any) => ({
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

    let filterTime = [
        { value: "7-day", name: "7 Hari Terakhir" },
        { value: "1-month", name: "1 Bulan Terakhir" },
        { value: "1-year", name: "1 Tahun Terakhir" },
    ];

    let selected: any;
</script>

<div class="relative px-8 md:px-16 mt-24">
    <Navbar />

    <div class="content">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Pengumuman</Heading>
        </div>

        <!-- <div class="w-full mb-8 grid">
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
        </div> -->

        <div class="group-card w-full pb-24">
            {#each announcements as announcement}
                <Cards
                    imageUrl={announcement.attachment
                        ? `/storage/assets/uploads/${announcement.attachment}`
                        : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
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
