<script lang="ts">
    import axios from "axios";
    import { twMerge } from "tailwind-merge";

    import { Create } from "../../schema";
    import { setCookie, getCookie } from "./../../Utils/Cokies";
    import { Login } from "@R/schema";

    import Table from "@C/Temp/Table.svelte";
    import Autorized from "@C/Temp/Layout/Autorized.svelte";

    import CreateDues from "../../Components/Temp/Modal/CreateDues.svelte";
    import EditDues from "../../Components/Temp/Modal/EditDues.svelte";
    import DeleteDues from "../../Components/Temp/Modal/DeleteDues.svelte";

    let showCreateDues = { show: false };
    let showUpdateDues = { show: false, targetId: null };
    let showDeleteDues = { show: false, targetId: null };

    const getData = async () => {
        const response = await axios.get("/api/dues", {
            headers: {
                Accept: "*/*",
                Authorization: `Bearer ${getCookie("token")}`,
            },
        });

        return response.data;
    };
</script>

<Autorized>
    <div
        class="w-full xl:h-[50px] md:max-h-[50px] lg:max-h-[50px] flex justify-center align-middle pl-10 pr-2 py-5"
    >
        <div class="w-full h-[90%]">
            <div class="text-gray-900 bg-gray-200 rounded-lg">
                <div class="p-4 flex justify-between">
                    <h1 class="text-3xl">Dues</h1>
                    <button
                        type="button"
                        class="text-lg bg-green-500 hover:bg-green-700 text-white py-1 rounded focus:outline-none focus:shadow-outline w-fit h-full flex justify-between align-middle text-center px-6"
                        on:click={() => {
                            showCreateDues.show = true;
                        }}
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 mt-1 fill-white"
                            viewBox="0 0 24 24"
                            ><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"
                            ></path></svg
                        >
                        <span> New Dues </span>
                    </button>
                </div>
                <div class="px-3 py-4 flex justify-center">
                    <Table>
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3 px-5">Type</th>
                                <th class="text-left p-3 px-5">Description</th>
                                <th class="text-left p-3 px-5">Amount Dues</th>
                                <th class="text-left p-3 px-5">Amount Fund</th>
                                <th class="text-left p-3 px-5">Status</th>
                                <th class="text-left p-3 px-5">Belongs to RT</th
                                >
                                <th class="text-left p-3 px-5"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {#await getData()}
                                <span class="w-full p-5">LOADING</span>
                            {:then data}
                                {#each data.data as { id, typeDues, description, amt_dues, amt_fund, status, rt_id }, idx}
                                    <tr class="border-b">
                                        <td class="text-left p-3 px-5">
                                            {typeDues}
                                        </td>
                                        <td class="text-left p-3 px-5">
                                            {description}
                                        </td>
                                        <td>{amt_dues}</td>
                                        <td>{amt_fund}</td>
                                        <td
                                            >{status
                                                ? "Active"
                                                : "Nonactive"}</td
                                        >
                                        <td class="text-center p-3 px-5"
                                            >{rt_id.id}</td
                                        >
                                        <td class="text-center p-3 px-5">
                                            <div
                                                class="flex justify-between gap-2"
                                            >
                                                <button
                                                    type="button"
                                                    class="text-lg bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                    on:click={() => {
                                                        showUpdateDues.targetId =
                                                            id;
                                                        showUpdateDues.show = true;
                                                    }}
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    class="text-lg bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                    on:click={() => {
                                                        showDeleteDues.targetId =
                                                            id;
                                                        showDeleteDues.show = true;
                                                    }}
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                {/each}
                            {/await}
                        </tbody>
                    </Table>
                </div>
            </div>
        </div>
    </div>
</Autorized>

<EditDues
    bind:showState={showUpdateDues.show}
    bind:targetId={showUpdateDues.targetId}
/>
<DeleteDues
    bind:showState={showDeleteDues.show}
    bind:targetId={showDeleteDues.targetId}
/>
<CreateDues bind:showState={showCreateDues.show} />
