<script lang="ts">
    import { Heading } from "flowbite-svelte";
    import Navbar from "@C/LandingPage/Navbar.svelte";
    import Footer from "@C/LandingPage/Footer.svelte";
    import axiosInstance from "axios";
    import { onMount } from "svelte";
    const axios = axiosInstance.create();

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

    const getRTData = async (): Promise<void> => {
        try {
            const response = await axios.get<{ data: RT[] }>("/api/rt");
            dataRT = response.data.data;
        } catch (error) {
            console.error("Error fetching RT data:", error);
        }
    };

    onMount(getRTData);

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
</script>

<div class="relative px-8 md:px-16 mt-24">
    <Navbar />

    <div class="content">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Ketua RW</Heading>
        </div>

        <div class="flex md:flex-row flex-col mb-16">
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
            class="list-rt grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-8 md:px-32 pb-24"
        >
            {#each dataRT as rt}
                <div class="rounded-lg shadow-lg">
                    <img
                        src={rt.civils[0]?.leader_id
                            ? rt.civils[0].leader_id
                            : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                        alt=""
                        class="w-full object-cover rounded-lg h-auto object-top"
                    />
                    <div class="p-4">
                        <Heading tag="h5" class="mb-2"
                            >{rt.civils[0]?.fullName}</Heading
                        >
                        <p class="text-gray-500">
                            Ketua RT {rt.number} / {rt.civils[0]?.leader_id ||
                                3}
                        </p>
                        <p class="text-gray-500">{rt.civils[0].phone}</p>
                        <p class="text-gray-500">{rt.civils[0].address}</p>
                    </div>
                </div>
            {/each}
        </div>
    </div>

    <Footer />
</div>
