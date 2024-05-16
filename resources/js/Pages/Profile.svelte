<script lang="ts">
    import { page } from "@inertiajs/svelte";

    import Layout from "./Layout.svelte";
    import Pict from "@C/Profile/Pict.svelte";
    import Password from "@C/Profile/Password.svelte";

    import axiosInstance from "axios";
    import GeneralInfo from "@C/Profile/GeneralInfo.svelte";

    const user = $page.props.auth.user;
    const axios = axiosInstance.create({ withCredentials: true });
    let builder = {};

    export const rebuild = () => {
        builder = {};
    };

    const getUserData = async (id: string = "") => {
        const response = await axios.get(`/api/user/${encodeURI(id)}`);

        return response.data;
    };
</script>

<Layout>
    {#key builder}
        {#await getUserData(user.id) then data}
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <Pict {data} on:comp={rebuild} />
                    <Password />
                </div>
                <div>
                    <GeneralInfo {data} />
                </div>
            </div>
        {/await}
    {/key}
</Layout>
