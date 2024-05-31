<script lang="ts">
    import Layout from "./Layout.svelte";
    import { onMount } from "svelte";
    import axiosInstance from "axios";
    import {
        Button,
        Card,
        Table,
        Input,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Badge,
    } from "flowbite-svelte";
    import { Select, Label } from "flowbite-svelte";
    import { page } from "@inertiajs/svelte";

    const axios = axiosInstance.create();

    let role: string = $page.props.auth.user.role;
    let rt_id: string = $page.props.auth.user.rt_id;
    let value: string;
    let disabledSelectRT: boolean = false;

    if (role === "RT") {
        disabledSelectRT = true;
        value = rt_id;
    } else {
        value = "";
    }

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

    interface RTFormatted {
        id: number;
        name: string;
        number: number;
    }

    let dataRT: RTFormatted[] = [];
    let data: any;
    // const token = getCookie("token");
    // if (!token) {
    //     console.error("No token found");
    //     throw new Error("No token found");
    // }

    const getData = async (filter: string) => {
        try {
            const response = await axios.get(
                `/api/civilian/head/${encodeURIComponent(filter)}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initData = async () => {
        value = $page.props.auth.user.rt_id;
        dataRT = await getRTData();
        data = await getData(value);
    };

    const getRTData = async (): Promise<RTFormatted[]> => {
        const response = await axios.get("/api/rt/dd", {
            headers: {
                Accept: "*/*",
            },
        });

        // console.log("API RT Data Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((rt: RT) => ({
                id: rt.id,
                name: `RT ${rt.number}`,
                number: rt.number,
            }));
        } else {
            throw new Error("Unexpected response format");
        }
    };

    onMount(async () => {
        try {
            await initData();
            // console.log(dataRT);
            // console.log(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    const handleSelectChange = async () => {
        if (!value) return;

        const response = await getData(value);
        data = response;
    };
</script>

<Layout>
    <div
        class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex flex-col p-4 sm:p-6 w-full mb-8"
    >
        <h5
            class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-4"
        >
            Filter Data Pembayaran Penduduk
        </h5>
        <form action="" method="post">
            <div
                class="flex flex-col md:flex-row justify-around items-start md:items-center min-w-full"
            >
                <div
                    class="flex flex-1 justify-center items-center lg:px-4 mb-2 md:mb-0"
                >
                    <Label for="select-rt" class="mr-2">RT</Label>
                    <Select
                        class="max-w-xs"
                        id="select-rt"
                        placeholder="Pilih RT"
                        bind:value
                        {disabledSelectRT}
                        on:change={handleSelectChange}
                    >
                        {#each dataRT as { id, name, number }, idx}
                            {#if id == $page.props.auth.user.rt_id}
                                <option value={id} selected>{name}</option>
                            {:else}
                                <option value={id}>{name}</option>
                            {/if}
                        {/each}
                    </Select>
                </div>
                <div class="flex flex-1 justify-center items-center lg:px-4">
                    <Label for="nama_warga" class="mr-2">Cari warga</Label>
                    <Input
                        class="mr-4 max-w-xs"
                        type="text"
                        id="nama_warga"
                        placeholder="Cari Nama Warga"
                    />
                    <Button>Cari</Button>
                </div>
            </div>
        </form>
    </div>

    <Table tableClass="mb-0">
        <TableHead>
            <TableHeadCell>Nama Lengkap</TableHeadCell>
            <TableHeadCell>Alamat</TableHeadCell>
            <TableHeadCell class="text-center"
                >Status Kependudukan</TableHeadCell
            >
            <TableHeadCell>
                <span class="sr-only">Edit</span>
            </TableHeadCell>
        </TableHead>
        <TableBody tableBodyClass="divide-y">
            {#if data}
                {#each data.data as item, idx}
                    <TableBodyRow>
                        <TableBodyCell>{item?.fullName}</TableBodyCell>
                        <TableBodyCell>{item.address}</TableBodyCell>
                        <TableBodyCell class="text-center">
                            <Badge color="green">Tetap</Badge>
                        </TableBodyCell>
                        <TableBodyCell>
                            <Button
                                href={`/iuran/detail?rt=${encodeURIComponent(item.rt_id.id)}&civ=${encodeURIComponent(item.id)}`}
                                >Detail</Button
                            >
                        </TableBodyCell>
                    </TableBodyRow>
                {/each}
            {/if}
            <!-- dummy -->
            <!-- <TableBodyRow>
                <TableBodyCell>Joko Anwar</TableBodyCell>
                <TableBodyCell>Jl. Pahlawan No. 456</TableBodyCell>
                <TableBodyCell class="text-center">
                    <Badge color="green">Tetap</Badge>
                </TableBodyCell>
                <TableBodyCell>
                    <Button href="/iuran/detail">Detail</Button>
                </TableBodyCell>
            </TableBodyRow> -->
            <!-- end dummy -->
            <!-- {#each data as dt}
                <TableBodyRow>
                    <TableBodyCell>{dt.fullName}</TableBodyCell>
                    <TableBodyCell>{dt.address}</TableBodyCell>
                    {#if dt.residentstatus == "PermanentResident"}
                        <TableBodyCell class="text-center">
                            <Badge color="green">Tetap</Badge>
                        </TableBodyCell>
                    {:else if dt.residentstatus == "ContractResident"}
                        <TableBodyCell class="text-center">
                            <Badge color="indigo">Kontrak</Badge>
                        </TableBodyCell>
                    {:else if dt.residentstatus == "Kos"}
                        <TableBodyCell class="text-center">
                            <Badge color="yellow">Kos</Badge>
                        </TableBodyCell>
                    {/if}
                    <TableBodyCell>
                        <Button href="">Detail</Button>
                    </TableBodyCell>
                </TableBodyRow>
            {/each} -->
        </TableBody>
    </Table>
</Layout>
