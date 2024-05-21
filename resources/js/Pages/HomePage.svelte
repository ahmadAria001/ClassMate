<script lang="ts">
    import Layout from "./Layout.svelte";
    import {
        Card,
        Popover,
        Table,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
    } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { onMount } from "svelte";
    import { QuestionCircleSolid } from "flowbite-svelte-icons";

    const axios = axiosInstance.create({ withCredentials: true });

    let announcement: any;
    let citizenActivity: any;

    const getNews = async () => {
        const response = await axios.get(`/api/news/lts`, {
            headers: {
                Accept: "application/json",
            },
        });

        return response.data;
    };

    const getCitizenEvents = async () => {
        const response = await axios.get(`/api/docs/activity/lts`);

        return response.data;
    };

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

    onMount(async () => {
        announcement = await getNews();
        citizenActivity = await getCitizenEvents();
    });
</script>

<Layout>
    <Card class="max-w-full">
        <h5
            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Pengumuman
        </h5>
        {#if announcement}
            {#each announcement.data as item, idx}
                <h5 class="font-bold mt-2 text-black dark:text-white">
                    {item.title}
                </h5>
                <p class="mb-2">{item.desc}</p>
                <hr />
            {/each}
        {/if}
    </Card>
    <Card class="mt-3 max-w-full">
        <h5
            class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            Agenda Kegiatan Masyarakat
        </h5>
        <Table>
            <TableHead>
                <TableHeadCell>Nama</TableHeadCell>
                <TableHeadCell>Lokasi</TableHeadCell>
                <TableHeadCell>Waktu</TableHeadCell>
            </TableHead>
            {#if citizenActivity}
                <TableBody>
                    {#each citizenActivity.data as item, idx}
                        <TableBodyRow>
                            <TableBodyCell>
                                <div
                                    class="flex justify-between align-middle gap-2"
                                >
                                    <span class="w-full truncate">
                                        {item.name}
                                    </span>
                                    <QuestionCircleSolid
                                        id={`title-${item.id}`}
                                    />
                                </div>
                            </TableBodyCell>
                            <TableBodyCell>
                                {item.location}
                            </TableBodyCell>
                            <TableBodyCell
                                class="text-center uppercase flex justify-center"
                            >
                                <span class="text-center">
                                    {dateFormatter(item.startDate * 1000)}
                                    <br />
                                    {new Date(
                                        item.startDate * 1000,
                                    ).toLocaleTimeString(undefined, {
                                        hour12: false,
                                    })}
                                </span>
                                <span class="ms-5 text-center">
                                    {dateFormatter(item.endDate * 1000)}
                                    <br />
                                    {new Date(
                                        item.endDate * 1000,
                                    ).toLocaleTimeString(undefined, {
                                        hour12: false,
                                    })}
                                </span>
                            </TableBodyCell>
                        </TableBodyRow>

                        <Popover
                            class="w-64 text-sm text-white"
                            title="Deskripsi"
                            triggeredBy={`#title-${item.id}`}
                        >
                            {item.docs_id.description}
                        </Popover>
                    {/each}
                </TableBody>
            {/if}
        </Table>
    </Card>
</Layout>
