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

    let dataRT: any;
    let currentRW: any;

    const getRTData = async () => {
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

    onMount(async () => {
        dataRT = await getRTData();
        currentRW = await getRW();
    });
</script>

<div class="relative px-8 md:px-16 mt-24">
    <Navbar />

    <div class="content">
        <div class="text-center mb-8">
            <Heading tag="h3" class="mb-2">Ketua RW</Heading>
        </div>

        {#if currentRW}
            <div class="flex md:flex-row flex-col mb-16">
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
        {/if}

        <div
            class="list-rt grid grid-cols-2 md:grid-cols-3 gap-4 lg:gap-8 md:px-32 pb-24"
        >
            {#if dataRT}
                {#each dataRT as rt}
                    <div class="rounded-lg shadow-lg">
                        <img
                            src={rt.leader_id?.pict
                                ? `/storage/assets/uploads/${rt.leader_id?.pict}`
                                : "https://redthread.uoregon.edu/files/original/affd16fd5264cab9197da4cd1a996f820e601ee4.png"}
                            alt=""
                            class="w-full object-cover rounded-lg h-auto object-top"
                        />
                        <div class="p-4">
                            <Heading tag="h5" class="mb-2"
                                >{rt.leader_id
                                    ? rt.leader_id?.civilian_id.fullName
                                    : ""}</Heading
                            >
                            <p class="text-gray-500">
                                Ketua RT {rt.number} / RW 3
                            </p>
                            <p class="text-gray-500">
                                {rt.leader_id
                                    ? rt.leader_id?.civilian_id.phone
                                    : ""}
                            </p>
                            <p class="text-gray-500">
                                {rt.leader_id
                                    ? rt.leader_id?.civilian_id.address
                                    : ""}
                            </p>
                        </div>
                    </div>
                {/each}
            {/if}
        </div>
    </div>

    <Footer />
</div>
