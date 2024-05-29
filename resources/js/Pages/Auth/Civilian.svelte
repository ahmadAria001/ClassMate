<script lang="ts">
    import axios from "axios";
    import { twMerge } from "tailwind-merge";

    import { Create } from "../../schema";

    import { Login } from "@R/schema";

    import Table from "@C/Temp/Table.svelte";
    import Autorized from "@C/Temp/Layout/Autorized.svelte";

    import CreateRt from "../../Components/Temp/Modal/CreateRT.svelte";
    import EditRT from "../../Components/Temp/Modal/EditRT.svelte";
    import DeleteRT from "../../Components/Temp/Modal/DeleteRT.svelte";

    import CreateCivil from "../../Components/Temp/Modal/CreateCivil.svelte";
    import EditCivil from "../../Components/Temp/Modal/EditCivil.svelte";
    import DeleteCivil from "../../Components/Temp/Modal/DeleteCivil.svelte";

    import CreateFamily from "../../Components/Temp/Modal/CreateFamily.svelte";
    import EditFamily from "../../Components/Temp/Modal/EditFamily.svelte";
    import DeleteFamily from "../../Components/Temp/Modal/DeleteFamily.svelte";
    // "@C//Temp/Modal.svelte";

    let CreateRTModal: any = null;
    let showCreate = false;
    let showUpdateRT = { show: false, target: null, leader: null };
    let showDeleteRT = { show: false, target: null, leader: null };

    let showCreateFam = { show: false };
    let showUpdateFam = { show: false, targetId: null };
    let showDeleteFam = { show: false, targetId: null };

    let showCreateCiv = { show: false };
    let showUpdateCiv = { show: false, targetId: null };
    let showDeleteCiv = { show: false, targetId: null };

    const getData = async () => {
        const response = await axios.get("/api/rt", {
            headers: {
                Accept: "*/*",
                Authorization: `Bearer ${getCookie("token")}`,
            },
        });

        return response.data;
    };

    const expandElement = (ElementID: string) => {
        const accordion = document.getElementById(ElementID);

        if (accordion === undefined || accordion === null) return;

        accordion.classList.toggle("hidden");
    };
    const rotateCollapseBtn = (ElementID: string) => {
        const accordion = document.getElementById(ElementID);

        if (accordion === undefined || accordion === null) return;

        accordion.classList.toggle("rotate-180");
    };
</script>

<Autorized>
    <div
        class="w-full xl:h-[50px] md:max-h-[50px] lg:max-h-[50px] flex justify-center align-middle pl-10 pr-2 py-5"
    >
        <div class="w-full h-[90%]">
            <div class="text-gray-900 bg-gray-200 rounded-lg">
                <div class="p-4 flex justify-between">
                    <h1 class="text-3xl">Civilian(RT)</h1>
                    <button
                        type="button"
                        class="text-lg bg-green-500 hover:bg-green-700 text-white py-1 rounded focus:outline-none focus:shadow-outline w-fit h-full flex justify-between align-middle text-center px-6"
                        on:click={() => {
                            showCreate = true;
                        }}
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 mt-1 fill-white"
                            viewBox="0 0 24 24"
                            ><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"
                            ></path></svg
                        >
                        <span> New RT </span>
                    </button>
                </div>
                <div class="px-3 py-4 flex justify-center">
                    <Table>
                        <tbody>
                            <tr class="border-b">
                                <th class="text-left p-3 px-5">ID</th>
                                <th class="text-left p-3 px-5">Leader</th>
                                <th></th>
                                <th></th>
                            </tr>
                            {#await getData()}
                                <span class="w-full p-5">LOADING</span>
                            {:then data}
                                {#each data.data as { id, leader_id, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by, family }, idx}
                                    <tr
                                        class="border-b hover:bg-orange-100 bg-gray-100"
                                    >
                                        <td class="p-3 px-5">
                                            <span>
                                                {id}
                                            </span>
                                        </td>
                                        <td class="p-3 px-5">
                                            <span>
                                                {#if leader_id}
                                                    {leader_id.civilian_id
                                                        .fullName}
                                                {:else}
                                                    {leader_id}
                                                {/if}
                                            </span>
                                        </td>
                                        <td class="p-3 px-5">
                                            <div
                                                class="flex justify-between gap-2"
                                            >
                                                <button
                                                    type="button"
                                                    class="text-lg bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                    on:click={() => {
                                                        showUpdateRT.target =
                                                            id;

                                                        if (leader_id)
                                                            showUpdateRT.leader =
                                                                leader_id.id;
                                                        showUpdateRT.show = true;
                                                    }}
                                                >
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    class="text-lg bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                    on:click={() => {
                                                        showDeleteRT.target =
                                                            id;

                                                        if (leader_id)
                                                            showDeleteRT.leader =
                                                                leader_id;
                                                        showDeleteRT.show = true;
                                                    }}
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                        <td
                                            class="p-3 px-5 flex w-full h-full align-middle justify-center"
                                            ><button
                                                type="button"
                                                class="text-lgw-full h-full"
                                                on:click={() => {
                                                    expandElement(
                                                        `child-fable-${idx}`,
                                                    );
                                                    rotateCollapseBtn(
                                                        `svg-child-fable-${idx}`,
                                                    );
                                                }}
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    class="w-7 h-7 fill"
                                                    id={`svg-child-fable-${idx}`}
                                                >
                                                    <path
                                                        d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"
                                                    >
                                                    </path>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- { id, nik, fullName, birthplace, birthdate, residentstatus, family_id } -->
                                    <tr
                                        class="border-b hover:bg-orange-200 bg-gray-100 hidden"
                                        id={`child-fable-${idx}`}
                                    >
                                        <td
                                            class="bg-slate-200 relative overflow-x-auto"
                                            colspan="4"
                                        >
                                            <span
                                                class="ml-2 my-2 font-semibold w-full text-right"
                                            >
                                                Family
                                            </span>
                                            <table
                                                class="w-[100%] text-sm text-left rtl:text-right m-auto"
                                            >
                                                <thead>
                                                    <th
                                                        class="text-left p-3 px-5"
                                                        >ID</th
                                                    >
                                                    <th
                                                        class="text-left p-3 px-5"
                                                        >NKK</th
                                                    >
                                                    <th
                                                        class="text-left p-3 px-5"
                                                        >RT_ID</th
                                                    >
                                                    <th
                                                        class="text-left p-3 px-5"
                                                        >STATUS</th
                                                    >
                                                    <th
                                                        class="text-left p-3 px-5"
                                                    ></th>
                                                </thead>
                                                <tbody>
                                                    {#each family as { id, nkk, residentstatus, rt_id, created_at, created_by, updated_at, updated_by, deleted_at, deleted_by, civil }, idxf}
                                                        <tr>
                                                            <td
                                                                class="p-3 px-5"
                                                            >
                                                                <span>
                                                                    {id}
                                                                </span>
                                                            </td>
                                                            <td
                                                                class="p-3 px-5"
                                                            >
                                                                <span>
                                                                    {nkk}
                                                                </span>
                                                            </td>
                                                            <td
                                                                class="p-3 px-5 text-left"
                                                            >
                                                                <span>
                                                                    {rt_id}
                                                                </span>
                                                            </td>
                                                            <td
                                                                class="p-3 px-5 w-5"
                                                            >
                                                                <span
                                                                    class="truncate"
                                                                >
                                                                    {residentstatus}
                                                                </span>
                                                            </td>
                                                            <td
                                                                class="p-3 px-5 w-5"
                                                            >
                                                                <div
                                                                    class="flex justify-between gap-2"
                                                                >
                                                                    <button
                                                                        type="button"
                                                                        class="text-lg bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                                        on:click={() => {
                                                                            showUpdateFam.show = true;
                                                                            showUpdateFam.targetId =
                                                                                id;
                                                                        }}
                                                                    >
                                                                        Edit
                                                                    </button>
                                                                    <button
                                                                        type="button"
                                                                        class="text-lg bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                                        on:click={() => {
                                                                            showDeleteFam.targetId =
                                                                                id;
                                                                            showDeleteFam.show = true;
                                                                        }}
                                                                    >
                                                                        Delete
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td
                                                                class="p-3 px-5 flex w-full h-full align-middle justify-center"
                                                                ><button
                                                                    type="button"
                                                                    class="text-lgw-full h-full"
                                                                    on:click={() => {
                                                                        expandElement(
                                                                            `child-gable-${id}`,
                                                                        );
                                                                        rotateCollapseBtn(
                                                                            `svg-child-gable-${id}`,
                                                                        );
                                                                    }}
                                                                >
                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        viewBox="0 0 24 24"
                                                                        class="w-7 h-7 fill"
                                                                        id={`svg-child-gable-${id}`}
                                                                    >
                                                                        <path
                                                                            d="M16.293 9.293 12 13.586 7.707 9.293l-1.414 1.414L12 16.414l5.707-5.707z"
                                                                        >
                                                                        </path>
                                                                    </svg>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr
                                                            class={twMerge(
                                                                "transition-all ease-in duration-200 hidden p-5 w-full",
                                                            )}
                                                            id={`child-gable-${id}`}
                                                        >
                                                            <td
                                                                class="bg-slate-300 relative overflow-x-auto w-full"
                                                                colspan="6"
                                                            >
                                                                <span
                                                                    class="ml-2 my-2 font-semibold w-full text-right"
                                                                >
                                                                    Family
                                                                    Member
                                                                </span>
                                                                <table
                                                                    class="w-full text-sm text-left rtl:text-right m-auto"
                                                                >
                                                                    <thead>
                                                                        <tr>
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >ID</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >NIK</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >FULLNAME</th
                                                                            >

                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >BIRTH
                                                                                PLACE</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >BIRTH
                                                                                DATE</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >STATUS</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"
                                                                                >FAMILY
                                                                                ID</th
                                                                            >
                                                                            <th
                                                                                class="text-left p-3 px-5"

                                                                            ></th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        {#each civil as { id, nik, fullName, birthplace, birthdate, residentstatus, family_id }, idxc}
                                                                            <tr>
                                                                                <td
                                                                                    class="p-3 px-5 wfi"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {id}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {nik}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5 text-left"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {fullName}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {birthplace}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {new Date(
                                                                                            birthdate *
                                                                                                1000,
                                                                                        ).toDateString()}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {residentstatus}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <span
                                                                                    >
                                                                                        {family_id}
                                                                                    </span>
                                                                                </td>
                                                                                <td
                                                                                    class="p-3 px-5"
                                                                                >
                                                                                    <div
                                                                                        class="flex justify-between gap-2"
                                                                                    >
                                                                                        <button
                                                                                            type="button"
                                                                                            class="text-lg bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                                                            on:click={() => {
                                                                                                showUpdateCiv.targetId =
                                                                                                    id;
                                                                                                showUpdateCiv.show = true;
                                                                                            }}
                                                                                        >
                                                                                            Edit
                                                                                        </button>
                                                                                        <button
                                                                                            type="button"
                                                                                            class="text-lg bg-red-600 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline w-full h-full"
                                                                                            on:click={() => {
                                                                                                showDeleteCiv.targetId =
                                                                                                    id;
                                                                                                showDeleteCiv.show = true;
                                                                                            }}
                                                                                        >
                                                                                            Delete
                                                                                        </button>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        {/each}
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    {/each}
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                {/each}
                            {/await}
                        </tbody>
                    </Table>
                </div>
                <div class="w-full flex justify-end gap-2">
                    <button
                        type="button"
                        class="text-lg bg-green-500 hover:bg-green-700 text-white py-1 rounded focus:outline-none focus:shadow-outline w-fit h-full flex justify-between align-middle text-center px-6"
                        on:click={() => {
                            showCreateFam.show = true;
                        }}
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 mt-1 fill-white"
                            viewBox="0 0 24 24"
                            ><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"
                            ></path></svg
                        >
                        <span> New Family </span>
                    </button>
                    <button
                        type="button"
                        class="text-lg bg-green-500 hover:bg-green-700 text-white py-1 rounded focus:outline-none focus:shadow-outline w-fit h-full flex justify-between align-middle text-center px-6"
                        on:click={() => {
                            showCreateCiv.show = true;
                        }}
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-5 h-5 mt-1 fill-white"
                            viewBox="0 0 24 24"
                            ><path d="M15 2.013H9V9H2v6h7v6.987h6V15h7V9h-7z"
                            ></path></svg
                        >
                        <span> New Civilian </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</Autorized>
<EditRT
    bind:showState={showUpdateRT.show}
    bind:dataPointed={showUpdateRT.target}
    bind:leader={showUpdateRT.leader}
/>
<DeleteRT
    bind:showState={showDeleteRT.show}
    bind:dataPointed={showDeleteRT.target}
    bind:leader={showDeleteRT.leader}
/>
<CreateRt bind:showState={showCreate} />

<EditFamily
    bind:showState={showUpdateFam.show}
    bind:targetId={showUpdateFam.targetId}
/>
<DeleteFamily
    bind:showState={showDeleteFam.show}
    bind:targetId={showDeleteFam.targetId}
/>
<CreateFamily bind:showState={showCreateFam.show} />

<EditCivil
    bind:showState={showUpdateCiv.show}
    bind:targetId={showUpdateCiv.targetId}
/>
<DeleteCivil
    bind:showState={showDeleteCiv.show}
    bind:targetId={showDeleteCiv.targetId}
/>
<CreateCivil bind:showState={showCreateCiv.show} />
